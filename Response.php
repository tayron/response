<?php

namespace Tayron;

use \InvalidArgumentException;

/**
 * Classe que trata e gerencia informações de cabeçalho da requisição
 *
 * @author Tayron Miranda <dev@tayron.com.br>
 */
final class Response 
{
    /**
     * Armazena uma lista de cabeçalhos
     * 
     * @var array
     */
    private $listHeaders = array();
    
    /**
     * @var Singleton reference to singleton instance
     */
    private static $instance;

    /**
     * Response::__construct
     *
     * Impede com que o objeto seja instanciado
     */
    final private function __construct() 
    {
        
    }

    /**
     * Response::__clone
     *
     * Impede que a classe Requisição seja clonada
     *
     * @throws Execao Lança execção caso o usuário tente clonar este classe
     *
     * @return void
     */
    final public function __clone() 
    {
        throw new Exception('A classe Request não pode ser clonada.');
    }

    /**
     * Response::__wakeup
     *
     * Impede que a classe Requisição execute __wakeup
     *
     * @throws Execao Lança execção caso o usuário tente executar este método
     *
     * @return void
     */
    final public function __wakeup() 
    {
        throw new Exception('A classe Request não pode executar __wakeup.');
    }

    /**
     * Response::getInstance
     *
     * Retorna uma instância única de uma classe.
     *
     * @staticvar Singleton $instancia A instância única dessa classe.
     *
     * @return Singleton A Instância única.
     */
    public static function getInstance() 
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return self::$instance;
    }    
    
    /**
     * Response:: setHeader
     *
     * Método que seta cabeçalho de resposta da página
     * 
     * @param string $value Mensagem de cabeçalho de resposta
     * @param boolean $replace True para substituir um cabeçalho já existente
     * @param int $httpResponseCode Código de resposta do servidor
     * 
     * @throws InvalidArgumentException
     * 
     * @exemple:
     *  setHeader("HTTP/1.0 404 Not Found")
     *  setHeader("Location: http://www.example.com/") - Redirect browser
     *  setHeader('WWW-Authenticate: Negotiate')
     *  setHeader('WWW-Authenticate: NTLM', false)
     *  setHeader('Content-Type: application/pdf')
     *  setHeader('Content-Disposition: attachment; filename="downloaded.pdf"')
     *  setHeader('Cache-Control: no-cache, must-revalidate'); - HTTP/1.1
     *  setHeader('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); Date in the past
     * 
     * @return void
     */    
    public function setHeader($value, $replace = true, $httpResponseCode = null)
    {
        if(is_null($value)){
            throw new InvalidArgumentException('Não é possível setar header com valor nulo');
        }
        
        $key = current(explode(' ', $value));
        $header = array(
            'value' => $value,
            'replate' => $replace,
            'httpResponseCode' => $httpResponseCode            
        );

        if(array_key_exists($key, $this->listHeaders) && $replace == true){
            $this->listHeaders[$key] = $header;            
        }else if(array_key_exists($key, $this->listHeaders) && $replace == false){
            $headerOfList = $this->listHeaders[$key];
            $this->listHeaders[$key] = array($headerOfList, $header);
        }else{
            $this->listHeaders[$key] = $header;
        }
    }
    
    /**
     * Response::display
     * 
     * Método que envia os cabeçalhos de requisição
     * 
     * @return void
     */
    public function display()
    {
        foreach ($this->listHeaders as $header)
        {
            if(isset($header['value'])){
                header($header['value'], $header['replace'], $header['httpResponseCode']);        
            }else{
                array_walk($header, function($item){
                    header($item['value'], $item['replace'], $item['httpResponseCode']);            
                });
            }            
        }
    }
}
