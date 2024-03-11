<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Migrations\Configuration\EntityManager\ManagerRegistryEntityManager;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\VarDumper\VarDumper;

class Controller extends AbstractController
{
    private ?string $api = "http://localhost:45431/cars/";
    private ?string $apiUsers = "http://localhost:45431/user/";
    private ?string $isLogged = "false";

    #[Route('/')]
    public function homepage(): Response
    {
        return $this->render("home.html.twig",['logged' => $this->isLogged]);
    }

    public function addFormTwig(): Response
    {
        return $this->render("create/add.html.twig");
    }

    public function addCar(): Response
    {
        $model = $_GET['model'];
        $manufacturer = $_GET['manufacturer'];
        $price = $_GET['price'];
        $features = $_GET['features'];

        $client = HttpClient::create();
        $urlConection = $this->api . "add";
        $response = $client->request('POST', $urlConection, ['json' => [
            'model' => $model,
            'manufacturer' =>  $manufacturer,
            'price' =>  $price,
            'features' =>  $features
        ]]);

        $content = $response->getContent();
        $carArray = json_decode($content, true);


        $message = "Coche importado correctamente";
        return $this->render("response.html.twig", ['message' => $message,]);
    }


    public function showAllCarsTwig(): Response
    {
        return $this->render("read/showForm.html.twig");
    }

    public function showProductById(): Response
    {

        $id = $_GET['id'];

        $client = HttpClient::create();
        $urlConection = $this->api . "get/" . $id;
        $response = $client->request('GET', $urlConection);
        $content = $response->getContent();
        $carArray = json_decode($content, true);
        
        return $this->render("read/cars.html.twig", ['carsArray' => $carArray,]);

    }

    public function showAllCars(): Response
    {
        $client = HttpClient::create();
        $urlConection = $this->api . "get";
        $response = $client->request('GET', $urlConection);
        $content = $response->getContent();
        $carsArray = json_decode($content, true);

        return $this->render("read/cars.html.twig", ['carsArray' => $carsArray,]);

    }

    public function selectCarToUpdateTwig(): Response
    {

        $client = HttpClient::create();
        $urlConection = $this->api . "get";
        $response = $client->request('GET', $urlConection);
        $content = $response->getContent();
        $carsArray = json_decode($content, true);

        return $this->render("update/updateSelect.html.twig", ['carsArray' => $carsArray,]);

    }

    public function sendToUpdateCarForm(): Response
    {

        $id = $_GET['id'];

        $client = HttpClient::create();
        $urlConection = $this->api . "get/" . $id;
        $response = $client->request('GET', $urlConection);
        $content = $response->getContent();
        $carArray = json_decode($content, true);
        
        if($carArray == "Coche no encontrado"){
            $message = $carArray;
            return $this->render("response.html.twig", ['message' => $message,]);
        }else{
            return $this->render("update/update.html.twig", ['carArray' => $carArray[0],]);
        }
    }

    public function updateCar(): Response
    {
        $id = $_GET['id'];
        $model = $_GET['model'];
        $manufacturer = $_GET['manufacturer'];
        $price = $_GET['price'];
        $features = $_GET['features'];

        $client = HttpClient::create();
        $urlConection = $this->api . "edit/" . $id;
        $response = $client->request('PUT', $urlConection, ['json' => [
            'model' => $model,
            'manufacturer' =>  $manufacturer,
            'price' =>  $price,
            'features' =>  $features
        ]]);

        $content = $response->getContent();
        $carArray = json_decode($content, true);

        return $this->render("read/cars.html.twig", ['carsArray' => $carArray,]);
    }



    public function deleteFormTwig(): Response
    {
        return $this->render("delete/delete.html.twig");
    }

    public function deleteCar(): Response
    {
        $id = $_GET['id'];

        $client = HttpClient::create();
        $urlConection = $this->api . "delete/" . $id;
        $response = $client->request('DELETE', $urlConection);
        $content = $response->getContent();
        $message = json_decode($content, true);

        return $this->render("response.html.twig", ['message' => $message,]);
    }

    public function showLoginAndSignUpFormTwig(){
        return $this->render("session/session.html.twig");
    }

    public function signUpUser(): Response{

        $email = $_POST['email'];
        $password = $_POST['password'];

        $userJSON["json"] = [
            "email"=>$email,
            "password"=>$password
        ];

        $client = HttpClient::create();
        $urlConection = $this->apiUsers . "register";
        $response = $client->request('POST', $urlConection, $userJSON);
        $content = $response->getContent();
        $message = json_decode($content, true);

        return $this->render("response.html.twig", ['message' => $message,]);
    }

    public function loginUser(): Response{

        $email = $_POST['email'];
        $password = $_POST['password'];

        $userJSON["json"] = [
            "email"=>$email,
            "password"=>$password
        ];

        $client = HttpClient::create();
        $urlConection = $this->apiUsers . "checklogin";
        $response = $client->request('POST', $urlConection, $userJSON);

        $content = $response->getContent();
        $message = json_decode($content, true);

        $finalMessage = "Ha iniciado sesion";
        if($message['value'] == "true"){
            setcookie("sessionEmail",$email, time() + 60 * 60 * 24);

            if($message["role"][0] == "ROLE_ADMIN"){
                setcookie("admin",'true', time() + 60 * 60 * 24);
            }
            

            $this->isLogged = "true";
        }else{
            $this->isLogged = "false";
            $finalMessage = "No es posible iniciar sesion";
        }


        return $this->render("response.html.twig", ['message' => $finalMessage,]);
    }

    public function deleteCookie(): Response{
        setcookie("sessionEmail",'', time() - 3600);
        setcookie("admin",'', time() - 3600);
        return $this->render("response.html.twig", ['message' => 'Sesion Cerrada',]);
    }
}
