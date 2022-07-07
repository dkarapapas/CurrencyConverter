<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{

    #[Route("/", name: "index")]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/api/init')]
    public function initDatabase(EntityManagerInterface $em, ParameterBagInterface $bag): Response
    {
        $json = file_get_contents($bag->get('root_directory') . DIRECTORY_SEPARATOR . 'currencies.json');
        $currencies = json_decode($json, true);

        foreach ($currencies as $currency) {
            $c = new Currency();
            $c->setCurrencyIdentifier($currency['currency_identifier'])
                ->setCurrencyIcon($currency['currency_icon'])
                ->setDescription($currency['description']);
            $em->persist($c);
        }
        $em->flush();

        return new Response(null, 200);
    }

    #[Route('/api/currencies', methods: 'GET')]
    public function getCurrencies(EntityManagerInterface $em): Response
    {
        $currencies = $em->getRepository(Currency::class)->findAll();
        $data = [];

        foreach ($currencies as $currency) {
            $data[] = [
                'id' => $currency->getId(),
                'description' => $currency->getDescription(),
                'icon' => $currency->getCurrencyIcon(),
                'identifier' => $currency->getCurrencyIdentifier()
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/currenciesRates', methods: 'GET')]
    public function getCurrenciesRates(EntityManagerInterface $em): Response
    {
        $currenciesRates = $em->getRepository(CurrencyRate::class)->findAll();
        dump($currenciesRates);
        $data = [];

        foreach ($currenciesRates as $currencyRate) {
            $data[] = [
                'currencyFromID' => $currencyRate->getCurrencyFrom()->getid(),
                'currencyFromDesc' => $currencyRate->getCurrencyFrom()->getDescription(),
                'currencyToID' => $currencyRate->getCurrencyTo()->getid(),
                'currencyToDesc' => $currencyRate->getCurrencyTo()->getDescription(),
                'rate' => $currencyRate->getRate(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/currencies/getRates/{currencyFrom}/{currencyTo}', methods: 'GET')]
    public function getRates(ManagerRegistry $doctrine, Request $request): Response
    {
        $repository = $doctrine->getRepository(CurrencyRate::class);
        $currencyRate = $repository->findOneBy([
            'currencyFrom' => $request->attributes->get('currencyFrom'),
            'currencyTo' => $request->attributes->get('currencyTo')
        ]);

        return new Response($currencyRate->getRate(), 200);
    }

    #[Route('/api/currencies/{id}', methods: 'DELETE')]
    public function delete(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $currency = $entityManager->getRepository(Currency::class)->find(['id' => $request->attributes->get('id')]);
        $entityManager->remove($currency);
        $entityManager->flush();

        return new Response(null, 200);
    }

    #[Route('/api/currenciesRates/{currencyFrom}/{currencyTo}', methods: 'DELETE')]
    public function deleteRates(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $currencyRates = $entityManager->getRepository(CurrencyRate::class)->
                        find(['currencyFrom' => $request->attributes->get('currencyFrom'),
                              'currencyTo' => $request->attributes->get('currencyTo')]);
        $entityManager->remove($currencyRates);
        $entityManager->flush();

        return new Response(null, 200);
    }

    #[Route('/api/currencies/', name: 'save', methods: 'POST')]
    public function save(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();

        $currency = new Currency();
        $currency->setCurrencyIdentifier($data['currencyIdentifier']);
        $currency->setDescription($data['description']);
        $currency->setCurrencyIcon($data['icon']);

        $errors = $validator->validate($currency);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new Response($errorsString);
        }

        $entityManager->persist($currency);
        $entityManager->flush();
        return new Response($currency->getId(), 201);
    }

    #[Route('/api/currenciesRates/', name: 'saveRates', methods: 'POST')]
    public function saveRates(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): Response
    {
        $dataCurrenciesRates = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();

        $currencyFrom = $entityManager->getRepository(Currency::class)->
                                        find(['id' => $dataCurrenciesRates['currencyFromID']]);
        $currencyTo = $entityManager->getRepository(Currency::class)->
                                      find(['id' => $dataCurrenciesRates['currencyToID']]);

        $currencyRates = new CurrencyRate();
        $currencyRates->setCurrencyFrom($currencyFrom) ;
        $currencyRates->setCurrencyTo($currencyTo);
        $currencyRates->setRate($dataCurrenciesRates['rates']);

        $errors = $validator->validate($currencyRates);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new Response($errorsString);
        }

        $data = [
            'currencyFromID' => $currencyRates->getCurrencyFrom()->getid(),
            'currencyFromDesc' => $currencyRates->getCurrencyFrom()->getDescription(),
            'currencyToID' => $currencyRates->getCurrencyTo()->getid(),
            'currencyToDesc' => $currencyRates->getCurrencyTo()->getDescription(),
            'rate' => $currencyRates->getRate(),
        ];

        $entityManager->persist($currencyRates);
        $entityManager->flush();
        return $this->json($data);
    }

    #[Route('/api/currencies/', name: 'update', methods: 'PUT')]
    public function update(ManagerRegistry $doctrine, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();

        $currency = $entityManager->getRepository(Currency::class)->find(['id' => $data['id']]);
        $currency->setCurrencyIdentifier($data['currencyIdentifier']);
        $currency->setDescription($data['description']);
        $currency->setCurrencyIcon($data['icon']);

        $entityManager->flush();
        return new Response(null, 200);
    }

    #[Route('/api/currenciesRates/', name: 'updateRates', methods: 'PUT')]
    public function updateRates(ManagerRegistry $doctrine, Request $request): Response
    {
        $dataCurrenciesRates = json_decode($request->getContent(), true);
        $entityManager = $doctrine->getManager();

        $currenciesRates = $entityManager->getRepository(CurrencyRate::class)->
                                               find(['currencyFrom' =>$dataCurrenciesRates['currencyFromID'],
                                                     'currencyTo' => $dataCurrenciesRates['currencyToID']]);

        $currenciesRates->setRate($dataCurrenciesRates['rates']);

        $entityManager->flush();
        return new Response(null, 200);
    }
}