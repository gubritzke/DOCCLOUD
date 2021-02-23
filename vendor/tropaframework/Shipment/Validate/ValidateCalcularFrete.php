<?php
namespace Tropaframework\Shipment\Validate;

/**
 * @author: Vitor Deco
 */
class ValidateCalcularFrete extends ValidateAbstract
{
	//erros
	const ERROR_DEFAULT = "Não foi possível calcular o frete!";
	const ERROR_REQUIRED_REMETENTE_CEP = "O cep do remetente está inválido!";
	const ERROR_REQUIRED_DESTINATARIO_CEP = "O cep do destinatário está inválido!";
	
}