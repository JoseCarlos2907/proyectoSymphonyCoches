<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cars', name: 'car')]
class CarController extends AbstractController
{
    #[Route('/add', name: 'add_car', methods:["POST"])]
    public function addCar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $car = new Car();
        $car->setModel($data["model"]);
        $car->setManufacturer($data["manufacturer"]);
        $car->setFeatures($data["features"]);
        $car->setPrice($data["price"]);
        
        $entityManager->persist($car);
        $entityManager->flush();

        return $this->json($car, Response::HTTP_CREATED);
    }

    #[Route('/get', name: 'car_get', methods:["GET"])]
    public function getCar(CarRepository $carRep): Response
    {
        $cars = $carRep->findAll();
        $carsJSON = [];
        foreach($cars as $car){
            $carsJSON[] = [
                "id" => $car->getId(),
                "model" => $car->getModel(),
                "manufacturer" => $car->getManufacturer(),
                "features" => $car->getFeatures(),
                "price" => $car->getPrice()
            ];
        };

        return $this->json($carsJSON);
    }

    #[Route('/get/{id}', name: 'car_get_id', methods:["GET"])]
    public function getCarById($id, CarRepository $carRep): Response
    {
        $car = $carRep->find($id);
        if(!$car)
            return $this->json("Coche no encontrado", Response::HTTP_OK);

        $carsJSON[] = [
            "id" => $car->getId(),
            "model" => $car->getModel(),
            "manufacturer" => $car->getManufacturer(),
            "features" => $car->getFeatures(),
            "price" => $car->getPrice()
        ];

        return $this->json($carsJSON, Response::HTTP_OK);
    }

    #[Route('/edit/{id}', name: 'car_edit', methods:["PUT"])]
    public function editCar($id, Request $request, CarRepository $carRep, EntityManagerInterface $em): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $car = $carRep->find($id);
        if(!$car)
            return $this->json("Coche no encontrado", Response::HTTP_NOT_FOUND);
        
        if(isset($data["model"]))
        $car->setModel($data["model"]);
        
        if(isset($data["manufacturer"]))
        $car->setManufacturer($data["manufacturer"]);

        if(isset($data["features"]))
        $car->setFeatures($data["features"]);

        if(isset($data["price"]))
        $car->setPrice($data["price"]);

        $em->persist($car);
        $em->flush();

        $carJSON[] = [
            [
                "id" => $car->getId(),
                "model" => $car->getModel(),
                "manufacturer" => $car->getManufacturer(),
                "features" => $car->getFeatures(),
                "price" => $car->getPrice()
            ]
        ];

        return $this->json($carJSON, Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'car_delete', methods:["DELETE"])]
    public function deleteCar($id, CarRepository $carRep, EntityManagerInterface $em): Response
    {
        $car = $carRep->find($id);
        if(!$car)
            return $this->json("Coche no encontrado", Response::HTTP_OK);

        $em->remove($car);
        $em->flush();

        return $this->json("Coche Eliminado", Response::HTTP_OK);
    }
}
