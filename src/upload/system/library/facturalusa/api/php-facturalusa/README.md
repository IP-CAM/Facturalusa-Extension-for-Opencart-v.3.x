# PHP - Facturalusa
Biblioteca para ajudar na interacção com o Software de Facturação Online www.facturalusa.pt via PHP. 

Visualize a API em https://facturalusa.pt/documentacao/api

## Requisitos

1. PHP >= 5.3.0
2. **[Composer](https://getcomposer.org/)**

## Instalação

```shell
$ composer require infolusa/php-facturalusa
```

## Endpoints
Todos os endpoints listados na documentação oficial estão disponíveis para utilizar nesta biblioteca. Os parâmetros a serem preenchidos no ```$array``` devem ser consultados na API.

#### Exemplo prático
```php
$facturalusa = new \Facturalusa\FacturalusaClient('api_token');
$customer = new \Facturalusa\Customer\Customer($facturalusa);
$customer->list();

print_r($facturalusa->response());

// Outro endpoint chamado
$customer->create(['param1' => 'value1']);
print_r($facturalusa->response());

// Outra forma de chamar
(new \Facturalusa\Customer\Customer($facturalusa))->create(['param1' => 'value1']);

// Também pode ser atribuída a resposta à variável
$response = $customer->list();
```

#### Respostas & status
Três funções são disponibilizadas:
1. ```$facturalusa->fail()``` - devolve se o pedido falhou
2. ```$facturalusa->success()``` - devolve se o pedido foi bem executado
3. ```$facturalusa->response()``` - devolve a resposta do pedido, tenha ou não falhado

As respostas diferem de endpoint para endpoint, pelo que deve consultar a documentação em https://facturalusa.pt/documentacao/api para saber que conteúdo é devolvido. Por exemplo, no caso de criação de um cliente, a informação devolvida é semelhante:
```curl
{
  "status": true,
  "data":
  {
    "id": ..,
    "code": ..,
  }
}
```
Para aceder, por exemplo, ao ID criado basta ```$facturalusa->response()->data->id```.

### Clientes endpoints
| Função | URL
| --- | --- |
| Customer\Customer->create($array) | https://facturalusa.pt/documentacao/api#clientes
| Customer\Customer->update($id, $array) | https://facturalusa.pt/documentacao/api#clientes-actualizar
| Customer\Customer->delete($id) | https://facturalusa.pt/documentacao/api#clientes-eliminar
| Customer\Customer->find($array) | https://facturalusa.pt/documentacao/api#clientes-procurar
| Customer\Customer->list($array) | https://facturalusa.pt/documentacao/api#clientes-lista
| Customer\CustomerOpenAccounts->find($id) | https://facturalusa.pt/documentacao/api#clientes-contas-aberto-singular
| Customer\CustomerOpenAccounts->download($id, $array) | https://facturalusa.pt/documentacao/api#clientes-contas-aberto-download
| Customer\CustomerOpenAccounts->sendEmail($id, $array) | https://facturalusa.pt/documentacao/api#clientes-contas-aberto-enviar-email
| Customer\CustomerOpenAccounts->list($array) | https://facturalusa.pt/documentacao/api#clientes-contas-aberto-lista

### Artigos endpoints
| Função | URL
| --- | --- |
| Item\Item->create($array) | https://facturalusa.pt/documentacao/api#artigos
| Item\Item->update($id, $array) | https://facturalusa.pt/documentacao/api#artigos-actualizar
| Item\Item->delete($id) | https://facturalusa.pt/documentacao/api#artigos-eliminar
| Item\Item->find($array) | https://facturalusa.pt/documentacao/api#artigos-procurar
| Item\Item->list($array) | https://facturalusa.pt/documentacao/api#artigos-lista
| Item\ItemStock->create($array) | https://facturalusa.pt/documentacao/api#artigos-stock-movimentos-criar
| Item\ItemStock->update($id, $array) | https://facturalusa.pt/documentacao/api#artigos-stock-movimentos-actualizar
| Item\ItemStock->delete($id) | https://facturalusa.pt/documentacao/api#artigos-stock-movimentos-eliminar
| Item\ItemStock->current($itemId) | https://facturalusa.pt/documentacao/api#artigos-stock-actual

### Vendas endpoints
| Função | URL
| --- | --- |
| Sale\Sale->create($array) | https://facturalusa.pt/documentacao/api#vendas
| Sale\Sale->update($id, $array) | https://facturalusa.pt/documentacao/api#vendas-actualizar
| Sale\Sale->delete($id) | https://facturalusa.pt/documentacao/api#vendas-eliminar
| Sale\Sale->cancel($id, $array) | https://facturalusa.pt/documentacao/api#vendas-cancelar
| Sale\Sale->duplicate($id, $array) | https://facturalusa.pt/documentacao/api#vendas-duplicar
| Sale\Sale->creditNote($id) | https://facturalusa.pt/documentacao/api#vendas-nota-credito
| Sale\Sale->debitNote($id) | https://facturalusa.pt/documentacao/api#vendas-nota-debito
| Sale\Sale->receipt($id) | https://facturalusa.pt/documentacao/api#vendas-recibo
| Sale\Sale->download($id, $array) | https://facturalusa.pt/documentacao/api#vendas-download
| Sale\Sale->sendEmail($id, $array) | https://facturalusa.pt/documentacao/api#vendas-enviar-email
| Sale\Sale->sendSms($id, $array) | https://facturalusa.pt/documentacao/api#vendas-enviar-sms
| Sale\Sale->sign($id, $array) | https://facturalusa.pt/documentacao/api#vendas-assinar-digitalmente
| Sale\Sale->generateMBReference($id) | https://facturalusa.pt/documentacao/api#vendas-gerar-ref-multibanco
| Sale\Sale->summary($array) | https://facturalusa.pt/documentacao/api#vendas-sumario
| Sale\Sale->find($array) | https://facturalusa.pt/documentacao/api#vendas-procurar
| Sale\Sale->list($array) | https://facturalusa.pt/documentacao/api#vendas-lista

### Recibos endpoints
| Função | URL
| --- | --- |
| Receipt\Receipt->create($array) | https://facturalusa.pt/documentacao/api#recibos
| Receipt\Receipt->update($id, $array) | https://facturalusa.pt/documentacao/api#recibos-actualizar
| Receipt\Receipt->delete($id) | https://facturalusa.pt/documentacao/api#recibos-eliminar
| Receipt\Receipt->cancel($id, $array) | https://facturalusa.pt/documentacao/api#recibos-cancelar
| Receipt\Receipt->download($id, $array) | https://facturalusa.pt/documentacao/api#recibos-download
| Receipt\Receipt->sendEmail($id, $array) | https://facturalusa.pt/documentacao/api#recibos-enviar-email
| Receipt\Receipt->sendSms($id, $array) | https://facturalusa.pt/documentacao/api#recibos-enviar-sms
| Receipt\Receipt->sign($id, $array) | https://facturalusa.pt/documentacao/api#recibos-assinar-digitalmente
| Receipt\Receipt->summary($array) | https://facturalusa.pt/documentacao/api#recibos-sumario
| Receipt\Receipt->find($array) | https://facturalusa.pt/documentacao/api#recibos-procurar
| Receipt\Receipt->list($array) | https://facturalusa.pt/documentacao/api#recibos-lista

### Subscrição endpoints
| Função | URL
| --- | --- |
| Subscription\Location->create($array) | https://facturalusa.pt/documentacao/api#subscricao-locais-criar
| Subscription\Location->update($id, $array) | https://facturalusa.pt/documentacao/api#subscricao-locais-actualizar
| Subscription\Location->delete($id) | https://facturalusa.pt/documentacao/api#subscricao-locais-eliminar
| Subscription\Location->find($array) | https://facturalusa.pt/documentacao/api#subscricao-locais-procurar
| Subscription\Location->list($array) | https://facturalusa.pt/documentacao/api#subscricao-locais-lista

### Administração endpoints
| Função | URL
| --- | --- |
| Administration\Administration->all() | https://facturalusa.pt/documentacao/api#administracao
| Administration\Category->create($array) | https://facturalusa.pt/documentacao/api#administracao-categorias-criar
| Administration\Category->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-categorias-actualizar
| Administration\Category->delete($id) | https://facturalusa.pt/documentacao/api#administracao-categorias-eliminar
| Administration\Category->find($array) | https://facturalusa.pt/documentacao/api#administracao-categorias-procurar
| Administration\Category->list($array) | https://facturalusa.pt/documentacao/api#administracao-categorias-lista
| Administration\Currency->create($array) | https://facturalusa.pt/documentacao/api#administracao-moedas-criar
| Administration\Currency->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-moedas-actualizar
| Administration\Currency->delete($id) | https://facturalusa.pt/documentacao/api#administracao-moedas-eliminar
| Administration\Currency->find($array) | https://facturalusa.pt/documentacao/api#administracao-moedas-procurar
| Administration\Currency->list($array) | https://facturalusa.pt/documentacao/api#administracao-moedas-lista
| Administration\DocumentType->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-actualizar
| Administration\DocumentType->communicateSerie($id, $serieId, $array) | https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-comunicar-serie
| Administration\DocumentType->find($array) | https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-procurar
| Administration\DocumentType->list($array) | https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-lista
| Administration\Employee->create($array) | https://facturalusa.pt/documentacao/api#administracao-colaboradores-criar
| Administration\Employee->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-colaboradores-actualizar
| Administration\Employee->delete($id) | https://facturalusa.pt/documentacao/api#administracao-colaboradores-eliminar
| Administration\Employee->find($array) | https://facturalusa.pt/documentacao/api#administracao-colaboradores-procurar
| Administration\Employee->list($array) | https://facturalusa.pt/documentacao/api#administracao-colaboradores-lista
| Administration\PaymentCondition->create($array) | https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-criar
| Administration\PaymentCondition->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-actualizar
| Administration\PaymentCondition->delete($id) | https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-eliminar
| Administration\PaymentCondition->find($array) | https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-procurar
| Administration\PaymentCondition->list($array) | https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-lista
| Administration\PaymentMethod->create($array) | https://facturalusa.pt/documentacao/api#administracao-formaspagamento-criar
| Administration\PaymentMethod->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-formaspagamento-actualizar
| Administration\PaymentMethod->delete($id) | https://facturalusa.pt/documentacao/api#administracao-formaspagamento-eliminar
| Administration\PaymentMethod->find($array) | https://facturalusa.pt/documentacao/api#administracao-formaspagamento-procurar
| Administration\PaymentMethod->list($array) | https://facturalusa.pt/documentacao/api#administracao-formaspagamento-lista
| Administration\Price->create($array) | https://facturalusa.pt/documentacao/api#administracao-precos-criar
| Administration\Price->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-precos-actualizar
| Administration\Price->delete($id) | https://facturalusa.pt/documentacao/api#administracao-precos-eliminar
| Administration\Price->find($array) | https://facturalusa.pt/documentacao/api#administracao-precos-procurar
| Administration\Price->list($array) | https://facturalusa.pt/documentacao/api#administracao-precos-lista
| Administration\Serie->create($array) | https://facturalusa.pt/documentacao/api#administracao-series-criar
| Administration\Serie->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-series-actualizar
| Administration\Serie->delete($id) | https://facturalusa.pt/documentacao/api#administracao-series-eliminar
| Administration\Serie->communicate($id) | https://facturalusa.pt/documentacao/api#administracao-series-comunicar
| Administration\Serie->find($array) | https://facturalusa.pt/documentacao/api#administracao-series-procurar
| Administration\Serie->list($array) | https://facturalusa.pt/documentacao/api#administracao-series-lista
| Administration\Serie->byDocumentType($array) | https://facturalusa.pt/documentacao/api#administracao-series-por-tipo-documento
| Administration\ShippingMode->create($array) | https://facturalusa.pt/documentacao/api#administracao-modosexpedicao-criar
| Administration\ShippingMode->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-modosexpedicao-actualizar
| Administration\ShippingMode->delete($id) | https://facturalusa.pt/documentacao/api#administracao-modosexpedicao-eliminar
| Administration\ShippingMode->find($array) | https://facturalusa.pt/documentacao/api#administracao-modosexpedicao-procurar
| Administration\ShippingMode->list($array) | https://facturalusa.pt/documentacao/api#administracao-modosexpedicao-lista
| Administration\Unit->create($array) | https://facturalusa.pt/documentacao/api#administracao-unidades-criar
| Administration\Unit->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-unidades-actualizar
| Administration\Unit->delete($id) | https://facturalusa.pt/documentacao/api#administracao-unidades-eliminar
| Administration\Unit->find($array) | https://facturalusa.pt/documentacao/api#administracao-unidades-procurar
| Administration\Unit->list($array) | https://facturalusa.pt/documentacao/api#administracao-unidades-lista
| Administration\VatRate->create($array) | https://facturalusa.pt/documentacao/api#administracao-taxasiva-criar
| Administration\VatRate->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-taxasiva-actualizar
| Administration\VatRate->delete($id) | https://facturalusa.pt/documentacao/api#administracao-taxasiva-eliminar
| Administration\VatRate->find($array) | https://facturalusa.pt/documentacao/api#administracao-taxasiva-procurar
| Administration\VatRate->list($array) | https://facturalusa.pt/documentacao/api#administracao-taxasiva-lista
| Administration\Vehicle->create($array) | https://facturalusa.pt/documentacao/api#administracao-veiculos-criar
| Administration\Vehicle->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-veiculos-actualizar
| Administration\Vehicle->delete($id) | https://facturalusa.pt/documentacao/api#administracao-veiculos-eliminar
| Administration\Vehicle->find($array) | https://facturalusa.pt/documentacao/api#administracao-veiculos-procurar
| Administration\Vehicle->list($array) | https://facturalusa.pt/documentacao/api#administracao-veiculos-lista
| Administration\Warehouse->create($array) | https://facturalusa.pt/documentacao/api#administracao-armazens-criar
| Administration\Warehouse->update($id, $array) | https://facturalusa.pt/documentacao/api#administracao-armazens-actualizar
| Administration\Warehouse->delete($id) | https://facturalusa.pt/documentacao/api#administracao-armazens-eliminar
| Administration\Warehouse->find($array) | https://facturalusa.pt/documentacao/api#administracao-armazens-procurar
| Administration\Warehouse->list($array) | https://facturalusa.pt/documentacao/api#administracao-armazens-lista

## Ajuda

Se tiver alguma dúvida ou questão, envie-nos uma mensagem através do formulário de contacto, via Suporte Ticket (dentro da sua subscrição) ou via email para geral@facturalusa.pt

## Licença

MIT
