<?php
namespace Tropaframework\Shipment\Validate;
use Tropaframework\Helper\Validate;

/**
 * @author: Vitor Deco
 */
abstract class ValidateAbstract extends Validate
{
	//lista de erros
	const ERROR_REQUIRED = "Todos os campos obrigatórios devem ser enviados!";
	const ERROR_CEP_AREA = "Infelizmente o CEP informado está fora da área de atendimento​!";
	const ERROR_RETURN_NULL = "Não foi possível calcular o frete!";
}