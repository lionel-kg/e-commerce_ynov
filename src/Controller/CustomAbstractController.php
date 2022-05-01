<?php

namespace App\Controller;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Abstract Class CustomAbstractController
 *
 * An abstract Controller with all generic method who's
 * gonna be used by all other Controller
 *
 * @package App\Controller
 */
abstract class CustomAbstractController extends AbstractController
{

    private const PASSWORD_LENGTH = 8;
    //150Mo for pictures
    private const FILE_SIZE_LIMIT_DEFAULT = 157286400;
    /**
     * checkParameters
     *
     * Check if all the asked parameters from the Request is here
     * @param array $parameters
     * @param array $parametersWaited
     * @return array[
     * "error" => "string",
     * "parameters" => [mixed]
     * ]
     */
    public function checkParameters(array $parameters, array $parametersWaited): array
    {
        $error = "";
        $newParameters = [];
        foreach ($parametersWaited as $field => $valueType) {
            $isOptional = false;
            if (substr($field, -4) === "_OPT") {
                $field = substr($field, 0, -4);
                $isOptional = true;
            }
            if (isset($parameters[$field]) === false) {
                if ($isOptional === false) {
                    $error = sprintf("Le champ '%s' est manquant", $field);
                    break;
                }

                if (in_array($valueType, ["bool", "boolean"])) {
                    $newParameters[$field] = false;
                } else {
                    $newParameters[$field] = NULL;
                }
                continue;
            }
            if (empty($parameters[$field]) && $parameters[$field] !== "0" && $parameters[$field] !== 0){
                if ($isOptional === true) {
                    $newParameters[$field] = NULL;
                    continue;
                }

                if (in_array($valueType, ["bool", "boolean"])) {
                    $newParameters[$field] = false;
                    continue;
                }

                $error = sprintf("Le champ '%s' est vide", $field);
                break;
            }
            [
                "error" => $errorCheckValue,
                "value" => $newValue
            ] = $this->checkValue($field, $parameters[$field], $valueType, $isOptional);
            if ($errorCheckValue !== "") {
                $error = $errorCheckValue;
                break;
            }

            $newParameters[$field] = $newValue;
        }
        return ["error" => $error, "parameters" => $newParameters];
    }

    /**
     * checkValue
     *
     * Check if all values are in the good types
     * @param string $field
     * @param $value
     * @param string $valueType
     * @param bool $isOptional
     * @return array[
     * "error" => "string",
     * "value" => "mixed",
     * ]
     */
    public function checkValue(string $field, $value, string $valueType, bool $isOptional): array
    {
        $error = "";
        $fileType = "";
        $arrayType = "";
        $dateFormat = "Y-m-d H:i:s";
        $multiple = false;
        if (strpos($valueType, "multiple") === 0) {
            $multiple = true;
            $valueType = substr($valueType, 9);
        }
        if (strpos($valueType, "explode") === 0) {
            $arrayType = substr($valueType, 8);
            $valueType = "explode";
        }
        if (strpos($valueType, "array") === 0) {
            $arrayType = substr($valueType, 6);
            $valueType = "array";
        }
        if (strpos($valueType, "file") === 0) {
            $fileType = substr($valueType, 5);
            $valueType = "file";
        }
        if (strpos($valueType, "date") === 0) {
            $dateFormat = substr($valueType, 5);
            $dateFormat = ($dateFormat === false) ? "Y-m-d H:i:s" : $dateFormat;
            $valueType = "date";
        }
        if ($isOptional === false && in_array($value, [NULL, false, ""], true)) {
            $error = sprintf("Le champ '%s' ne peut être vide", $field);
        }
        switch ($valueType) {
            case "string":
                if (is_string($value) === false) {
                    $error = sprintf("Le champ '%s' ne peut être du texte", $field);
                }
                break;
            case "int":
            case "integer":
            case "number":
                $newValue = (int)$value;
                if ($value !== (string)$newValue) {
                    $error = sprintf("Le champ '%s' doit être un numéro entier", $field);
                } else {
                    $value = $newValue;
                }
                break;
            case "float":
                $newValue = (float)$value;
                $newNewValue = (string)$newValue;
                $strlenValue = strlen(substr(strrchr($value, "."), 1));
                $strlenNewValue = strlen(substr(strrchr($newValue, "."), 1));
                if ($value !== $newNewValue && $strlenValue === $strlenNewValue) {
                    $error = sprintf("Le champ '%s' doit être un nombre", $field);
                } else {
                    $value = (float)number_format($newValue, 2, ".", "");
                }
                break;
            case "email":
                if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $error = sprintf("Le champ '%s' doit être un email valide", $field);
                }
                break;
            case "password":
                if (strlen($value) < self::PASSWORD_LENGTH) {
                    $error = sprintf("Le champ '%s' doit avoir au moins %d caractères", $field, self::PASSWORD_LENGTH);
                }
                break;
            case "file":
                $fileTypes = explode("_", $fileType);
                if (is_array($value) === false) {
                    $value = [$value];
                }
                $errorCheckFile = $this->checkFile($value, $fileTypes, $multiple);
                if ($errorCheckFile !== "") {
                    $error = $errorCheckFile;
                }
                break;
            case "tel":
                $filteredValue = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                $filteredValue = str_replace("-", "", $filteredValue);
                if (strlen($filteredValue) < 10 || strlen($filteredValue) > 14) {
                    $error = sprintf("Le champ '%s' n'est pas un numéro de tel valide", $field);
                } else {
                    $value = $filteredValue;
                }
                break;
            case "bool":
            case "boolean":
                $value = ($value === "true");
                break;
            case "explode":
            case "array":
                $toExplode = false;
                if ($valueType === "explode") {
                    $toExplode = true;
                }
                $valueArray = $value;
                if ($arrayType !== false) {
                    $valueArray = $this->checkArray($arrayType, $value, $toExplode);
                }
                if (empty($valueArray)) {
                    if ($isOptional === false) {
                        $error = sprintf("Le champ '%s' est vide", $field);
                    } else {
                        $value = null;
                    }
                } else {
                    $value = $valueArray;
                }
                break;
            case "date":
                $dateTime = $this->checkDateFormat($value, $dateFormat);
                if ($dateTime === false) {
                    $error = sprintf("Le champ '%s' n'est pas au format souhaiter", $field);
                } else {
                    $value = $dateTime;
                }
                break;
            case "url":
                if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                    $error = sprintf("Le champ '%s' n'est pas un liens valide", $field);
                }
                break;
            case "uuid":
                if ($this->checkUuid($value) === false) {
                    $error = sprintf("Le champ '%s' n'est pas un identifiant", $field);
                }
                break;
            default:
                break;
        }
        return ["error" => $error, "value" => $value];
    }

    /**
     * @param UploadedFile[]|array[UploadedFile[]] $files
     * @param string[] $fileType
     * @param bool $multiple
     * @return string
     */
    public function checkFile(array $files, array $fileType, bool $multiple = false): string
    {
        $error = "";
        if ($multiple === true) {
            foreach ($files as $arrayFile) {
                foreach ($arrayFile as $file) {
                    if (is_array($file) && isset($file["defaultValue"])) {
                        continue;
                    }
                    $error = $this->fileChecker($file, $fileType);
                    if ($error !== "") {
                        return $error;
                    }
                }
            }
        } else {
            foreach ($files as $file) {
                if (is_array($file) && isset($file["defaultValue"])) {
                    continue;
                }
                $error = $this->fileChecker($file, $fileType);
                if ($error !== "") {
                    return $error;
                }
            }
        }
        return $error;
    }

    /**
     * @param UploadedFile $file
     * @param array $fileType
     * @return string
     */
    public function fileChecker(UploadedFile $file, array $fileType): string
    {
        $error = "";
        if (!$file->isValid()) {
            $error = "Votre fichier n'est pas valide, peut-être qu'il n'est pas complé ou qu'il est corrompu ?";
        } elseif (!$file->isReadable()) {
            $error = "Nous n'arrivons pas à lire votre fichier";
        } elseif ($file->isExecutable()) {
            $error = "Vous ne pouez pas téléverser un executable";
        } elseif ($file->isLink()) {
            $error = "Vous ne pouez pas téléverser un lien";
        } elseif ($file->isDir()) {
            $error = "Vous ne pouez pas téléverser un dossier, séléctionné les fichiers contenu dedans";
        } else {
            $sizeLimit = self::FILE_SIZE_LIMIT_DEFAULT;
            $fileMimeType = $file->getMimeType();
            $fileTypeAccepted = false;
            $fileSizeExceed = false;
            foreach ($fileType as $type) {
                if ($type === "pdf") {
                    //50Mo for PDF
                    $sizeLimit = 52428800;
                    if ($sizeLimit < $file->getSize()) {
                        $fileSizeExceed = true;
                        break;
                    }
                    if ($fileMimeType === "application/pdf") {
                        $fileTypeAccepted = true;
                        break;
                    }
                } else {
                    if ($sizeLimit < $file->getSize()) {
                        $fileSizeExceed = true;
                        break;
                    }
                    if (strpos($fileMimeType, $type) === 0) {
                        $fileTypeAccepted = true;
                        break;
                    }
                }
            }
            if ($fileSizeExceed === true) {
                return "Fichier trop lourd";
            }
            if ($fileTypeAccepted === false) {
                return sprintf("Format de fichier non accepté, veuillez téléverser un fichier de type  %s", implode(", ", $fileType));
            }
        }
        return $error;
    }

    /**
     * @param string $arrayType
     * @param $value
     * @param bool $toExplode
     * @return array
     */
    public function checkArray(string $arrayType, $value, bool $toExplode = false): array
    {
        $func = static function ($el) {
            return trim($el);
        };
        if (in_array($arrayType, ["int", "integer"])) {
            $func = static function ($el) {
                $newEl = (int)$el;
                if ($el === (string)$newEl) {
                    return $newEl;
                }
                return null;
            };
        } elseif($arrayType === "float") {
            $func = static function ($el) {
                $newEl = (float)$el;
                if ($el === (string)$newEl) {
                    return $newEl;
                }
                return null;
            };
        } elseif (in_array($arrayType, ["bool", "boolean"])) {
            $func = static function ($el) {
                return ($el === "true");
            };
        }
        $array = $value;
        if ($toExplode) {
            $array = explode(",", $value);
        }
        $valueArray = array_map($func, $array);
        return $this->removeEmptyElementFromArray($valueArray);
    }

    /**
     * @param array $array
     * @return array
     */
    public function removeEmptyElementFromArray(array $array): array
    {
        $newArray = [];
        foreach ($array as $key => $item) {
            if ($item !== null && $item !== "") {
                $newArray[$key] = $item;
            }
        }
        return $newArray;
    }

    /**
     * sendJsonResponse
     *
     * Send a predefined Json Response
     * @param string $errorSuccess
     * @param string $message
     * @param null $data
     * @param int $httpCode
     * @return JsonResponse
     */
    public function sendJsonResponse(
        string $errorSuccess,
        string $message,
               $data = null,
        int $httpCode = Response::HTTP_OK
    ): JsonResponse
    {
        return new JsonResponse([$errorSuccess => $message, "data" => $data], $httpCode);
    }

    /**
     * sendError
     *
     * Alias to SendJsonResponse with error
     * @param string $message
     * @param string $messageDebug
     * @param null $data
     * @param int $httpCode
     * @return JsonResponse
     */
    public function sendError(
        string $message,
        string $messageDebug = "",
               $data = null,
        int $httpCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse
    {
        $resMessage = $message;
        if ($this->getParameter("APP_ENV") !== "prod") {
            $resMessage = $messageDebug;
            if (empty($messageDebug)) {
                $resMessage = $message;
            }
        }
        return $this->sendJsonResponse("error", $resMessage, $data, $httpCode);
    }

    /**
     * sendSuccess
     *
     * Alias to SendJsonResponse with success
     * @param $message
     * @param null $data
     * @param int $httpCode
     * @return JsonResponse
     */
    public function sendSuccess($message, $data = null, int $httpCode = Response::HTTP_OK): JsonResponse
    {
        return $this->sendJsonResponse("success", $message, $data, $httpCode);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getJwt(Request $request): string
    {
        return str_replace("Bearer ", '', $request->headers->get('Authorization'));
    }

    /**
     * getParameters
     * @param Request $request
     * @param array $filesFields
     * @return array
     */
    public function getParameters(Request $request, array $filesFields = []): array
    {
        $parameters = $request->request->all();
        $fileRequest = $request->files->all();
        if (!empty($fileRequest)) {
            foreach ($filesFields as $fileFieldName) {
                if (isset($fileRequest[$fileFieldName])) {
                    $files = $fileRequest[$fileFieldName];
                    foreach ($files as $index => $file) {
                        $parameters[$fileFieldName][$index] = $file;
                    }
                    ksort($parameters[$fileFieldName]);
                }
            }
        }
        return $parameters;
    }

    /**
     * @param object $entity
     * @return string
     */
    public function getHumanEntityName(object $entity): string
    {
        $path = explode('\\', get_class($entity));
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', array_pop($path));
    }

    /**
     * @param object $entity
     * @return string
     */
    public function getEntityName(object $entity): string
    {
        $path = explode('\\', get_class($entity));
        return lcfirst(array_pop($path));
    }

    /**
     * @param Request $request
     * @param object $serviceEntity
     * @param bool $addValue
     * @return JsonResponse
     */
    public function simpleEntityAdd(Request $request, object $serviceEntity, bool $addValue = false): JsonResponse
    {
        $entityName = $this->getHumanEntityName($serviceEntity);
        $parameters = $this->getParameters($request);
        $waitedParameters = ["name" => "string"];
        if ($addValue) {
            $waitedParameters["value"] = "string";
        }
        ["error" => $error] = $this->checkParameters($parameters, $waitedParameters);
        if ($error !== "") {
            return $this->sendError($error, $error);
        }
        if ($addValue) {
            [
                "error" => $error,
                "errorDebug" => $errorDebug
            ] = $serviceEntity->add($parameters["name"], $waitedParameters["value"]);
        } else {
            [
                "error" => $error,
                "errorDebug" => $errorDebug
            ] = $serviceEntity->add($parameters["name"]);
        }
        if ($error !== "") {
            return $this->sendError($error, $errorDebug);
        }
        return $this->sendSuccess(
            sprintf("%s successfully added ", $entityName),
            null,
            Response::HTTP_CREATED
        );
    }

    /**
     * @param object $serviceEntity
     * @return JsonResponse
     */
    public function simpleEntityFulfill(object $serviceEntity): JsonResponse
    {
        $entityName = $this->getHumanEntityName($serviceEntity);
        ["error" => $error, "errorDebug" => $errorDebug] = $serviceEntity->fulfill();
        if ($error !== "") {
            return $this->sendError($error, $errorDebug);
        }
        return $this->sendSuccess(
            sprintf("%s successfully added ", $entityName),
            null,
            Response::HTTP_CREATED
        );
    }

    /**
     * @param object $serviceEntity
     * @param array $groups
     * @return JsonResponse
     */
    public function simpleEntityGetAll(object $serviceEntity, array $groups = []): JsonResponse
    {
        $entityName = $this->getHumanEntityName($serviceEntity);
        $varName = $this->getEntityName($serviceEntity);
        ["error" => $error, "errorDebug" => $errorDebug, $varName => $result] = $serviceEntity->getAll($groups);
        if ($error !== "") {
            return $this->sendError($error, $errorDebug);
        }
        return $this->sendSuccess(sprintf("List of all %s", $entityName), [$varName => $result]);
    }

    /**
     * @param string $date
     * @param string $format
     * @return DateTime|false
     */
    public function checkDateFormat(string $date, string $format = 'Y-m-d H:i:s')
    {
        return DateTime::createFromFormat($format, $date);
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function checkUuid(string $uuid): bool
    {
        return !(!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1));
    }

    /**
     * @param string $uuid
     * @param Request $request
     * @param object $service
     * @param string $serializeGroup
     * @param string $returnName
     * @param string $entityName
     * @return JsonResponse
     */
    public function getInfoFromUuidSerializeGroup(
        string $uuid,
        Request $request,
        object $service,
        string $serializeGroup,
        string $returnName,
        string $entityName
    ): JsonResponse
    {
        $entityName = ucfirst($entityName);
        $errorMsg = "Erreur lors de la récupération de " . $entityName . " info ";
        $errorDebugMsg = $entityName . " Uuid not valid ";
        $jwt = $this->getJwt($request);
        if ($this->checkUuid($uuid) === false) {
            return $this->sendError($errorMsg, $errorDebugMsg);
        }
        [
            "error" => $error,
            "errorDebug" => $errorDebug,
            $returnName => $return
        ] = $service->getInfoFromUuid($jwt, $uuid, $serializeGroup, $entityName);
        if ($error !== "") {
            return $this->sendError($error, $errorDebug);
        }
        return $this->sendSuccess( $entityName . " Info ", [$returnName => $return]);
    }

    /**
     * @param string $url
     * @param string $baseUri
     * @return StreamedResponse
     * @throws GuzzleException
     */
    public function getResourceFromUrl(string $url, string $baseUri = "https://blog.alma-heritage.com"): StreamedResponse
    {
        $client = new Client([
            "base_uri" => $baseUri
        ]);
        $response = $client->request("GET", $url, ["stream" => true]);
        $body = $response->getBody();
        return new StreamedResponse(function() use ($body) {
            while (!$body->eof()) {
                echo $body->read(1024);
            }
        });
    }

}