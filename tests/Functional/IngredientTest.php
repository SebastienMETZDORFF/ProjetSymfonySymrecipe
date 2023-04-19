<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Ingredient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        
        // Recup urlGenerator
        $urlGenerator = $client->getContainer()->get('router');

        // Recup entity manager
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        // Se rendre sur la page de création d'un ingrédient
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.new'));

        $this->assertResponseIsSuccessful();

        // Gérer le formulaire
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'Ingrédient',
            'ingredient[price]' => floatval(33)
        ]);

        $client->submit($form);

        // Gérer la redirection
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        // Gérer l'alert box et la route
        $this->assertSelectorTextContains('div.alert-success', 
        'Votre ingrédient a été créé avec succès !');

        $this->assertRouteSame('ingredient.index');
    }

    public function testIfListIngredientsIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.index'));

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('ingredient.index');
    }

    public function testIfUpdateAnIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        
        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET, 
            $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
                'ingredient[name]' => 'Ingrédient 2',
                'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 
        'Votre ingrédient a été modifié avec succès !');

        $this->assertRouteSame('ingredient.index');
    }

    public function testIfDeleteAnIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        
        $user = $entityManager->find(User::class, 1);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET, 
            $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 
        'Votre ingrédient a été supprimé avec succès !');

        $this->assertRouteSame('ingredient.index');
    }
}
