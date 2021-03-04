<?php

namespace PublicBundle\Services\ValidatorInputService;

use AdministracionBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValidatorInputService {
    
    const VALIDATE_EMAIL = 'email';
    const VALIDATE_PHONE = 'phone';
    const VALIDATE_WEB = 'web';
    
    public function __construct() {
    }
    
    public function validar($inputData, $validate) {
        
        $validInput = true;
        
        switch($validate) {
            case ValidatorInputService::VALIDATE_EMAIL:
                $validInput = !strpos($inputData, '@') && !strpos(strtolower($inputData), 'arroba');
                break;
            case ValidatorInputService::VALIDATE_PHONE:
                $inputData = str_replace("-", "", $inputData);
                $inputData = str_replace(" ", "", $inputData);
                $inputData = str_replace("(", "", $inputData);
                $inputData = str_replace(")", "", $inputData);
                    
                $validInput =   !(bool)preg_match("/(?:(?:00)?549?)?0?(?:11|[2368]\d)(?:(?=\d{0,2}15)\d{2})??\d{8}/i", $inputData) &&
                                !(bool)preg_match("#\(?\d{2}\)?[\s\.-]?\d{4}[\s\.-]?\d{4}#i", $inputData) &&
                                !(bool)preg_match("#(\+34|0034|34)?[ -]*(6|7)[ -]*([0-9][ -]*){8}#i", $inputData) &&
                                !(bool)preg_match("#(\+34|0034|34)?[ -]*(8|9)[ -]*([0-9][ -]*){8}#i", $inputData);
                
                //var_dump($coincidencias);
                break;
            case ValidatorInputService::VALIDATE_WEB:
                $validInput =   !(bool)preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $inputData) &&
                                !strpos($inputData, '.com') && !strpos(strtolower($inputData), '.com') && !strpos(strtolower($inputData), 'puntocom') &&
                                !strpos($inputData, '.net') && !strpos(strtolower($inputData), '.net') && !strpos(strtolower($inputData), 'puntonet');
                break;
        }

        return $validInput;
        
        
    }
    
}
