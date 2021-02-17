<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Geocoder\Query\GeocodeQuery;
use Gedmo\Sluggable\Util\Urlizer;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/create", name="create_product")
     */
    public function createProduct(Request $request, UserInterface $user): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['pictures']->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );

                $product->setPictures($newFilename);
            }

            $product->setUid($user->getId());
            $product->setDate(new \DateTime());

            $httpClient = new \Http\Adapter\Guzzle6\Client();
            $provider = new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, null, 'AIzaSyB9feLVQ3SSdmgrerLfxYlUtIlxJfpwDrU');
            $geocoder = new \Geocoder\StatefulGeocoder($provider, 'fr');

            $result = $geocoder->geocodeQuery(GeocodeQuery::create($form->getData()->getCity()));
            $lat = $result->first()->getCoordinates()->getLatitude();
            $long = $result->first()->getCoordinates()->getLongitude();

            $coord = $lat . "," . $long;
            $product->setLongLat($coord);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('product/create.html.twig', [
            'createFormProduct' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}", name="modify_product")
     */
    public function modifyProduct(Request $request, int $id, ProductRepository $productRepository, UserInterface $user): Response
    {
        $product = $productRepository
            ->find($id);
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['pictures']->getData();

            if ($uploadedFile && !empty($uploadedFile)) {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );

                $product->setPictures($newFilename);
            }

            $product->setUid($user->getId());
            $product->setDate(new \DateTime());

            $httpClient = new \Http\Adapter\Guzzle6\Client();
            $provider = new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, null, 'AIzaSyB9feLVQ3SSdmgrerLfxYlUtIlxJfpwDrU');
            $geocoder = new \Geocoder\StatefulGeocoder($provider, 'fr');

            $result = $geocoder->geocodeQuery(GeocodeQuery::create($form->getData()->getCity()));
            $lat = $result->first()->getCoordinates()->getLatitude();
            $long = $result->first()->getCoordinates()->getLongitude();

            $coord = $lat . "," . $long;
            $product->setLongLat($coord);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('product/modify.html.twig', [
            'modifyFormProduct' => $form->createView()
        ]);
    }
}
