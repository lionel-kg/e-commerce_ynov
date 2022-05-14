<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\StockTaille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Taille;
use App\Entity\Section;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $data = $this->getFromJson();
        //dd($data);
        $sections = [];
        $tailles = [];

        foreach($data["tailles"] as $tai){
            $taille = new Taille();
            $taille->setLibelle($tai);
            $manager->persist($taille);
            $tailles[] = $taille;
        }

        foreach($data["sections"] as $sec){
            $section = new Section();
            $section->setLibelle($sec);
            $section->setImage("https://static.nike.com/a/images/t_PDP_1280_v1/f_auto/7a5deae8-7208-44ba-94e6-01f74793d174/tee-shirt-imprime-floral-sportswear-jdi-pour-gDwlms.jpg");
            $manager->persist($section);
            $sections[] = $section;
        }

        //dd($tailles);

        foreach($data['categories'] as $cat){
            $categorie = new Categorie();
            $categorie->setNom($cat);
            $categorie->setImage("https://static.nike.com/a/images/t_PDP_1280_v1/f_auto/7a5deae8-7208-44ba-94e6-01f74793d174/tee-shirt-imprime-floral-sportswear-jdi-pour-gDwlms.jpg");
            foreach($data['types-articles'] as $typeArt){
                $tabTypeArt = explode(".",$typeArt);
                if($tabTypeArt[0] === $cat){
                    foreach($data['articles']->articles as $art){
                        $ar = get_object_vars($art);
                        if($ar['type-article'] === $tabTypeArt[1]){
                            $article = new Produit();
                            $article->setNom($ar['libelle']);
                            $article->setCategorie($categorie);
                            $categorie->addProduit($article);
                            $article->setPrix($ar['prix_u']);
                            $article->setImage($ar['image']);
                            foreach($sections as $sec){
                                $article->addSection($sec);
                            }
                            foreach($ar['tailles'] as $tai){
                                foreach($tailles as $tail){
                                    if($tai === $tail->getLibelle()){
                                        $stockTaille = new StockTaille();
                                        $stockTaille->setProduit($article);
                                        $stockTaille->setTaille($tail);
                                        $stockTaille->setQte(mt_rand(0,20));
                                        $article->addStockTaille($stockTaille);
                                        $manager->persist($stockTaille);
                                    }
                                }
                            }
                            $manager->persist($article);
                        }
                    }
                }
            }
            $manager->persist($categorie);
        }

        /*
        $articles = json_decode(file_get_contents(__DIR__.'/../../articles.json'));
        $typeArticles = [];
        $sections = [];
        $tailles = [];
        foreach($articles as $article){
        }
        $typesArticles = $this->entityManager
            ->getRepository(TypeArticle::class)
            ->findAll();
        $sections = $this->entityManager
            ->getRepository(Section::class)
            ->findAll();
        $tailles = $this->entityManager
            ->getRepository(Taille::class)
            ->findAll();
        foreach($typesArticles as $typeArticle){
            foreach($sections as $section){
                $max = mt_rand(1,4);
                for($i = 0; $i < $max;$i++){
                    $article = new Article();
                    $article->setLibelle($typeArticle->getLibelle() . " - " . $section->getLibelle());
                    $article->setDescription($typeArticle->getLibelle() . " pour " . $section->getLibelle());
                    $article->setPrixU(mt_rand(1000, 5000) / 100);
                    foreach($tailles as $taille){
                        $randTaille = mt_rand(0,4);
                        if($randTaille > 0){
                            $quantiteTaille = new QuantiteTaille();
                            $quantiteTaille->setArticle($article);
                            $quantiteTaille->setTaille($taille);
                            $quantiteTaille->setQte(mt_rand(0,20));
                            $manager->persist($quantiteTaille);
                        }
                    }
                    $article->setImage("/img/example.png");
                    $article->setTypeArticle($typeArticle);
                    $article->addSection($section);
                    if(mt_rand(0,3)>2){
                        $article->addSection($sections[array_rand($sections)]);
                    }
                    $manager->persist($article);
                }
            }
        }
        */
        //dd($categorie,"/",$article,"/",$stockTaille,"/",$section);
        $manager->flush();
    }

    private function getFromJson(){
        $articles = json_decode(file_get_contents(__DIR__.'/../../produit.json'));

        $categories = [];
        $typeArticles = [];
        $sections = [];
        $tailles = [];

        foreach($articles->articles as $article){
            $art = get_object_vars($article);
            $categories[] = $art["categorie"];
            $typeArticles[] = $art["categorie"].".".$art["type-article"];
            foreach($art["sections"] as $section){
                $sections[] = $section;
            }
            foreach($art["tailles"] as $taille){
                $tailles[] = $taille;
            }
        }

        $categories = array_unique($categories);
        $typeArticles = array_unique($typeArticles);
        $sections = array_unique($sections);
        $tailles = array_unique($tailles);

        return ['articles' => $articles, 'categories' => $categories, 'types-articles' => $typeArticles, 'sections' => $sections, 'tailles' => $tailles];
    }

    /*public function getDependencies()
    {
        return array(
            TypeArticleFixtures::class,
            SectionFixtures::class,
            TailleFixtures::class,
        );
    }*/

    public static function getGroups(): array {
        return ["produit"];
    }
}
