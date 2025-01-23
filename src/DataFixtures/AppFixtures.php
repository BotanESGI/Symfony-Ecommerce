<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Orders;
use App\Entity\OrderItem;
use App\Entity\Review;
use App\Entity\Address;
use App\Entity\PhysicalProduct;
use App\Entity\DigitalProduct;
use App\Entity\Category;
use App\Entity\Invoice;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Enum\ReviewStatusEnum;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Couleurs catégories
        $colors = ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FFC300', '#581845', '#DAF7A6', '#C70039', '#900C3F', '#2E86C1'];

        // Catégories
        $categories = [];
        $categoryNames = [
            'Électronique', 'Appareils ménagers', 'Livres', 'Jouets', 'Mode',
            'Sports', 'Jardin', 'Musique', 'Bien-être', 'Voyage', 'Éducation'
        ];

        foreach ($categoryNames as $index => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setColor($colors[$index % count($colors)]); // Assigner une couleur
            $manager->persist($category);
            $categories[] = $category;
        }

        // Utilisateurs
        $users = [];
        $names = [
            ['Botan', 'ESGI'], ['Jane', 'Doe'], ['Alice', 'Durand'], ['Bob', 'Martin'], ['Émilie', 'Dupont'],
            ['Michel', 'Bernard'], ['Sarah', 'Lemoine'], ['David', 'Giraud'], ['Laura', 'Roux'], ['Christophe', 'Moreau']
        ];

        foreach ($names as $index => [$firstName, $lastName]) {
            $user = new User();
            $user->setEmail("utilisateur{$index}@exemple.com");
            $user->setPassword(password_hash('motdepasse', PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']);
            $user->setName($firstName);
            $user->setLastName($lastName);
            $user->setIsVerified(true);
            $manager->persist($user);
            $users[] = $user;
        }

        // Utilisateur Admin
        $admin = new User();
        $admin->setEmail('admin@exemple.com');
        $admin->setPassword(password_hash('admin', PASSWORD_BCRYPT));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setName('Administrateur');
        $admin->setLastName('Principal');
        $admin->setIsVerified(true);
        $manager->persist($admin);

        // Utilisateur Banni
        $bannedUser = new User();
        $bannedUser->setEmail('banni@exemple.com');
        $bannedUser->setPassword(password_hash('banni', PASSWORD_BCRYPT));
        $bannedUser->setRoles(['ROLE_BANNED']);
        $bannedUser->setName('Utilisateur');
        $bannedUser->setLastName('Banni');
        $bannedUser->setIsVerified(false);
        $manager->persist($bannedUser);

        // Produits
        $productsData = [
            [
                'name' => 'Casque audio',
                'description' => 'Casque audio sans fil avec réduction de bruit.',
                'type' => 'PHYSICAL',
                'price' => 150,
                'characteristics' => [
                    'poids' => '0.3 kg',
                    'dimensions' => '20x15x8 cm',
                    'autonomie_batterie' => '20 heures',
                    'annulation_de_bruit' => 'Oui',
                    'temps_de_charge' => '2 heures',
                    'portée' => '10 mètres',
                    'couleur' => 'Noir'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fd/Marshall_Monitor_Headphone_-_5._details_%282013-05-08_08.38.08_by_Dave_Kobrehel%29.jpg/1920px-Marshall_Monitor_Headphone_-_5._details_%282013-05-08_08.38.08_by_Dave_Kobrehel%29.jpg',
                'category' => 'Électronique',
            ],
            [
                'name' => 'Ordinateur portable',
                'description' => 'Ordinateur portable puissant pour les professionnels.',
                'type' => 'PHYSICAL',
                'price' => 1200,
                'characteristics' => [
                    'poids' => '2 kg',
                    'dimensions' => '35x24x2 cm',
                    'autonomie_batterie' => '10 heures',
                    'processeur' => 'Intel i7',
                    'ram' => '16 Go',
                    'stockage' => '512 Go SSD',
                    'couleur' => 'Argent'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Samsung_QX-511_%282%29.JPG/1920px-Samsung_QX-511_%282%29.JPG',
                'category' => 'Électronique',
            ],
            [
                'name' => 'Smartphone',
                'description' => 'Smartphone dernière génération avec un écran AMOLED.',
                'type' => 'PHYSICAL',
                'price' => 800,
                'characteristics' => [
                    'poids' => '0.2 kg',
                    'dimensions' => '15x7x0.8 cm',
                    'autonomie_batterie' => '24 heures',
                    'caméra' => '48 MP',
                    'stockage' => '128 Go',
                    'couleur' => 'Bleu'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Samsung_Galaxy_S10%2B.jpg/1024px-Samsung_Galaxy_S10%2B.jpg',
                'category' => 'Électronique',
            ],
            [
                'name' => 'Montre connectée',
                'description' => 'Montre connectée avec suivi de la santé.',
                'type' => 'PHYSICAL',
                'price' => 200,
                'characteristics' => [
                    'poids' => '0.1 kg',
                    'dimensions' => '4x4x1 cm',
                    'autonomie_batterie' => '7 jours',
                    'résistance_à_l_eau' => '5 ATM',
                    'suivi_de_la_santé' => 'Fréquence cardiaque, sommeil',
                    'couleur' => 'Noir'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Smartwatch-828786.jpg/1920px-Smartwatch-828786.jpg',
                'category' => 'Électronique',
            ],
            [
                'name' => 'Livre de cuisine',
                'description' => 'Recettes délicieuses pour toute la famille.',
                'type' => 'PHYSICAL',
                'price' => 25,
                'characteristics' => [
                    'poids' => '0.5 kg',
                    'dimensions' => '25x20x2 cm',
                    'nombre_de_pages' => '200 pages',
                    'type_de_couverture' => 'Broché',
                    'genre' => 'Cuisine',
                    'couleur' => 'Multicolore'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f8/COCINAR_CON_EL_LIBRO.png',
                'category' => 'Livres',
            ],
            [
                'name' => 'Roman d\'aventure',
                'description' => 'Un voyage épique à travers des mondes inconnus.',
                'type' => 'PHYSICAL',
                'price' => 15,
                'characteristics' => [
                    'poids' => '0.4 kg',
                    'dimensions' => '20x15x2 cm',
                    'nombre_de_pages' => '300 pages',
                    'type_de_couverture' => 'Relié',
                    'genre' => 'Aventure',
                    'couleur' => 'Multicolore'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/8/86/Verne_Tour_du_Monde.jpg',
                'category' => 'Livres',
            ],
            [
                'name' => 'Livre de science-fiction',
                'description' => 'Exploration des futurs possibles et des technologies avancées.',
                'type' => 'PHYSICAL',
                'price' => 20,
                'characteristics' => [
                    'poids' => '0.3 kg',
                    'dimensions' => '20x15x2 cm',
                    'nombre_de_pages' => '250 pages',
                    'type_de_couverture' => 'Broché',
                    'genre' => 'Science-fiction',
                    'couleur' => 'Multicolore'
                ],
                'image' => 'https://marketplace.canva.com/EADzX_z4AWg/1/0/1003w/canva-fonc%C3%A9-bleu-science-fiction-livre-couverture-smN23L-N62g.jpg',
                'category' => 'Livres',
            ],
            [
                'name' => 'Jeu de construction',
                'description' => 'Jeu de construction créatif pour enfants.',
                'type' => 'PHYSICAL',
                'price' => 50,
                'characteristics' => [
                    'poids'               => '1 kg',
                    'dimensions'          => '30x20x5 cm',
                    'matériau'            => 'Plastique',
                    'nombre_de_pieces'    => '150 pièces',
                    'age_recommande'      => 'À partir de 4 ans',
                    'color'               => 'Multicolore'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Constri.JPG/1920px-Constri.JPG',
                'category' => 'Jouets',
            ],
            [
                'name' => 'Poupée',
                'description' => 'Poupée en tissu douce pour enfants.',
                'type' => 'PHYSICAL',
                'price' => 30,
                'characteristics' => [
                    'poids'               => '0.3 kg',
                    'dimensions'          => '25x10x5 cm',
                    'matériau'            => 'Tissu',
                    'âge_recommandé'      => 'À partir de 3 ans',
                    'color'               => 'Rose'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Pierotti_wax_doll_from_Frederic_Aldis%2C_London%2C_01%2C_sitting_doll%2C_vested.jpg/1024px-Pierotti_wax_doll_from_Frederic_Aldis%2C_London%2C_01%2C_sitting_doll%2C_vested.jpg',
                'category' => 'Jouets',
            ],
            [
                'name' => 'Puzzle 1000 pièces',
                'description' => 'Un puzzle de 1000 pièces pour les amateurs de défis.',
                'type' => 'PHYSICAL',
                'price' => 25,
                'characteristics' => [
                    'poids'               => '1 kg',
                    'dimensions'          => '50x40x5 cm',
                    'matériau'            => 'Carton',
                    'nombre_de_pieces'    => '1000 pièces',
                    'niveau_de_difficulté' => 'Difficile',
                    'color'               => 'Multicolore'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Museum_Ravensburger_14.jpg/1280px-Museum_Ravensburger_14.jpg',
                'category' => 'Jouets',
            ],
            [
                'name' => 'T-shirt',
                'description' => 'T-shirt en coton doux, disponible en plusieurs tailles.',
                'type' => 'PHYSICAL',
                'price' => 20,
                'characteristics' => [
                    'poids'               => '0.2 kg',
                    'dimensions'          => '30x20x1 cm',
                    'matériau'            => 'Coton',
                    'tailles_disponibles' => 'S, M, L, XL',
                    'color'               => 'Blanc'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Blue_Tshirt.jpg',
                'category' => 'Mode',
            ],
            [
                'name' => 'Jean',
                'description' => 'Jean confortable avec une coupe moderne.',
                'type' => 'PHYSICAL',
                'price' => 50,
                'characteristics' => [
                    'poids'               => '0.5 kg',
                    'dimensions'          => '40x30x2 cm',
                    'matériau'            => 'Denim',
                    'tailles_disponibles' => 'S, M, L, XL',
                    'color'               => 'Bleu'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Levi%27s_501_raw_jeans.jpg/800px-Levi%27s_501_raw_jeans.jpg',
                'category' => 'Mode',
            ],
            [
                'name' => 'Veste en cuir',
                'description' => 'Veste en cuir élégante pour hommes et femmes.',
                'type' => 'PHYSICAL',
                'price' => 150,
                'characteristics' => [
                    'poids'               => '1 kg',
                    'dimensions'          => '50x40x2 cm',
                    'matériau'            => 'Cuir',
                    'tailles_disponibles' => 'S, M, L, XL',
                    'color'               => 'Noir'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/New-613-Schott_Perfecto-cut-out-and-shaded.jpg/640px-New-613-Schott_Perfecto-cut-out-and-shaded.jpg',
                'category' => 'Mode',
            ],
            [
                'name' => 'Raquette de tennis',
                'description' => 'Raquette légère en graphite.',
                'type' => 'PHYSICAL',
                'price' => 100,
                'characteristics' => [
                    'poids'               => '0.3 kg',
                    'dimensions'          => '68x27x5 cm',
                    'matériau'            => 'Graphite',
                    'niveau_de_joueur'    => 'Intermédiaire',
                    'color'               => 'Rouge'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/Tennis_racket_and_ball.JPG/255px-Tennis_racket_and_ball.JPG',
                'category' => 'Sports',
            ],
            [
                'name' => 'Ballon de football',
                'description' => 'Ballon de football de haute qualité.',
                'type' => 'PHYSICAL',
                'price' => 30,
                'characteristics' => [
                    'poids'               => '0.4 kg',
                    'dimensions'          => '22 cm de diamètre',
                    'matériau'            => 'Cuir synthétique',
                    'niveau_de_jeu'       => 'Amateur',
                    'color'               => 'Noir et blanc'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/1/1d/Football_Pallo_valmiina-cropped.jpg',
                'category' => 'Sports',
            ],
            [
                'name' => 'Tapis de yoga',
                'description' => 'Tapis de yoga antidérapant pour le confort.',
                'type' => 'PHYSICAL',
                'price' => 40,
                'characteristics' => [
                    'poids'               => '1 kg',
                    'dimensions'          => '180x60x0.5 cm',
                    'matériau'            => 'PVC',
                    'épaisseur'           => '0.5 cm',
                    'color'               => 'Violet'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/0/06/Ardha-Nav%C4%81sana.JPG',
                'category' => 'Sports',
            ],
            [
                'name' => 'Mixeur',
                'description' => 'Mixeur puissant pour préparer des smoothies.',
                'type' => 'PHYSICAL',
                'price' => 70,
                'characteristics' => [
                    'poids'               => '1.5 kg',
                    'dimensions'          => '40x15x15 cm',
                    'matériau'            => 'Plastique',
                    'puissance'           => '600 W',
                    'color'               => 'Noir'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/0/06/ElectricBlender.jpg',
                'category' => 'Appareils ménagers',
            ],
            [
                'name' => 'Table de jardin',
                'description' => 'Table en bois pour le jardin.',
                'type' => 'PHYSICAL',
                'price' => 300,
                'characteristics' => [
                    'poids'               => '15 kg',
                    'dimensions'          => '150x90x75 cm',
                    'matériau'            => 'Bois',
                    'capacite'            => '6 personnes',
                    'color'               => 'Brun'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Table_pliante.jpg/1920px-Table_pliante.jpg',
                'category' => 'Jardin',
            ],
            [
                'name' => 'Vélo de montagne',
                'description' => 'Vélo robuste pour les terrains difficiles.',
                'type' => 'PHYSICAL',
                'price' => 500,
                'characteristics' => [
                    'poids'               => '12 kg',
                    'dimensions'          => '175x60x100 cm',
                    'matériau'            => 'Aluminium',
                    'vitesse'             => '21 vitesses',
                    'color'               => 'Vert'
                ],
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/V%C3%A9lo_enfant_%C3%A0_courroie_et_cadre_aluminium.jpg/360px-V%C3%A9lo_enfant_%C3%A0_courroie_et_cadre_aluminium.jpg',
                'category' => 'Sports',
            ],
                [
                    'name' => 'E-book de cuisine',
                    'description' => 'Recettes de cuisine en format numérique.',
                    'type' => 'DIGITAL',
                    'price' => 10,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".PDF",
                    'downloadLink' => 'https://ebook.com/ebook_cuisine.pdf',
                    'image' => 'https://marketplace.canva.com/EADzX1V0eZo/1/0/1003w/canva-rouge-et-blanc-livre-de-cuisine-livre-couverture--84IPJPPzYE.jpg',
                    'category' => 'Livres',
                ],
                [
                    'name' => 'Jeu vidéo',
                    'description' => 'Jeu vidéo d\'aventure sur PC.',
                    'type' => 'DIGITAL',
                    'price' => 40,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".EXE",
                    'downloadLink' => 'https://jeu.com/downloads/jeu_video.exe',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Vg_history_icon_alt.svg/1280px-Vg_history_icon_alt.svg.png',
                    'category' => 'Jeux',
                ],
                [
                    'name' => 'Logiciel de montage vidéo',
                    'description' => 'Logiciel puissant pour le montage vidéo.',
                    'type' => 'DIGITAL',
                    'price' => 60,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".ZIP",
                    'downloadLink' => 'https://logiciel.com/downloads/logiciel-montage-vidéo.zip',
                    'image' => 'https://static-cse.canva.com/blob/1145314/Montagevideo.jpg',
                    'category' => 'Électronique',
                ],
                [
                    'name' => 'Album de musique numérique',
                    'description' => 'Téléchargez votre album préféré en format MP3.',
                    'type' => 'DIGITAL',
                    'price' => 15,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".MP3",
                    'downloadLink' => 'https://album.com/downloads/album-musique.mp3',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Audio_a.svg/2560px-Audio_a.svg.png',
                    'category' => 'Musique',
                ],
                [
                    'name' => 'Cours en ligne de programmation',
                    'description' => 'Apprenez la programmation avec ce cours en ligne.',
                    'type' => 'DIGITAL',
                    'price' => 50,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".PDF",
                    'downloadLink' => 'https://cours.com/downloads/cours-programmation.pdf',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/a/a4/JavaScript_code.png',
                    'category' => 'Éducation',
                ],
                [
                    'name' => 'Application de méditation',
                    'description' => 'Application numérique pour la méditation et la pleine conscience.',
                    'type' => 'DIGITAL',
                    'price' => 25,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".APK",
                    'downloadLink' => 'https://application.com/downloads/application_meditation.apk',
                    'image' => 'https://www.conseil-d-assureur.fr/wp-content/uploads/2022/02/app-meditation-petit-bambou.jpg',
                    'category' => 'Bien-être',
                ],
                [
                    'name' => 'Template de site web',
                    'description' => 'Template professionnel pour créer un site web facilement.',
                    'type' => 'DIGITAL',
                    'price' => 30,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".HTML",
                    'downloadLink' => 'https://template.com/downloads/template_site.html',
                    'image' => 'https://marketplace.canva.com/EAE6WTyrSQ0/2/0/1600w/canva-light-beige-sleek-and-simple-blogger-personal-website--7Q4-7tyJj4.jpg',
                    'category' => 'Électronique',
                ],
                [
                    'name' => 'Video formation en marketing digital',
                    'description' => 'Apprenez les stratégies de marketing digital.',
                    'type' => 'DIGITAL',
                    'price' => 75,
                    'fileSize' => rand(1, 5),
                    'fileType' => ".MP4",
                    'downloadLink' => 'https://formation.com/downloads/video_formation_marketing_digital.mp4',
                    'image' => 'https://media.licdn.com/dms/image/v2/C4D12AQFwOmSy4XaXbg/article-cover_image-shrink_600_2000/article-cover_image-shrink_600_2000/0/1621165601697?e=2147483647&v=beta&t=3EdRZZOvjZ3vAoEkEZPl9vwjXHPU88lQD8LEVrbX2mY',
                    'category' => 'Éducation',
                ],
        ];


        $products = [];

        foreach ($productsData as $data) {
            if ($data['type'] === 'PHYSICAL') {
                $product = new PhysicalProduct($categories[array_search($data['category'], $categoryNames)]);
                $product->setCharacteristics($data['characteristics']); // Utilisation des caractéristiques
            } else {
                $product = new DigitalProduct($categories[array_search($data['category'], $categoryNames)]);
                $product->setFilesize($data['fileSize']);
                $product->setFiletype($data['fileType']);
                $product->setDownloadLink($data['downloadLink']);
            }

            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setImage($data['image']);

            // Ajout de la catégorie
            $category = $categories[array_search($data['category'], $categoryNames)];
            $product->addCategory($category);

            // Tags
            $tags = [];
            $tagNames = ['SOLDE', 'BLACK FRIDAY', 'OPÉRATION SPÉCIALE', 'NOUVEAUTÉ', 'PROMO', 'EXCLUSIVITÉ'];
            for ($i = 0; $i < 2; $i++) {
                $tag = new Tag();
                $tag->setName($tagNames[array_rand($tagNames)]);
                $tag->setColor($colors[array_rand($colors)]);
                $manager->persist($tag);
                $tags[] = $tag;
            }

            foreach ($tags as $tag) {
                $product->addTag($tag);
            }

            // Avis
            foreach ($users as $user) {
                $review = new Review();
                $review->setContent('Produit excellent, je recommande !');
                $review->setRating(rand(4, 5));
                $review->setUser($user);
                $review->setProduct($product);
                $review->setStatus(ReviewStatusEnum::VALIDATED);
                $review->setDatePublication(new \DateTime());
                $manager->persist($review);
            }

            $manager->persist($product);
            $products[] = $product;
        }

        // Adresses
        foreach ($users as $index => $user) {
            $address = new Address();
            $address->setStreet("Rue {$index} Principale");
            $address->setCity("Ville {$index}");
            $address->setPostalCode("7500{$index}");
            $address->setUser($user);
            $manager->persist($address);
        }

        // Commandes
        foreach ($users as $user) {
            $order = new Orders();
            $order->setUser($user);
            $order->setTotal(rand(100, 1000));
            $order->setDate(new \DateTimeImmutable());
            $manager->persist($order);

            foreach (array_rand($products, 2) as $index) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($products[$index]);
                $orderItem->setQuantity(rand(1, 3));
                $orderItem->setOrder($order);
                $orderItem->setPrice($products[$index]->getPrice());
                $manager->persist($orderItem);
            }

            $invoice = new Invoice();
            $invoice->setTotalAmount($order->getTotal());
            $invoice->setUser($user);
            $invoice->setOrder($order);
            $manager->persist($invoice);
            $manager->flush();

            $pdfPath = '/path/to/invoices/invoice_' . $invoice->getId() . '.pdf';
            $invoice->setPdfPath($pdfPath);
            $order->setInvoice($invoice);
            $manager->persist($invoice);
        }

        $manager->flush();
    }

    private function getRandomColor(): string
    {
        $colors = ['Rouge', 'Vert', 'Bleu', 'Noir', 'Blanc', 'Jaune', 'Rose', 'Violet', 'Gris', 'Orange'];
        return $colors[array_rand($colors)];
    }
}
