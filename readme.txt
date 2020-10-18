=== Datadev - Jadlog for WooCommerce ===
Contributors: datadev, skydogtk
Tags: shipping, delivery, woocommerce, jadlog
Requires at least: 5.5.1
Tested up to: 5.5.1
Stable tag: 1.1.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integration between the Jadlog and WooCommerce

== Description ==

Utilize os métodos de entrega e serviços da Jadlog com a sua loja WooCommerce.

[Jadlog](http://www.jadlog.com.br/) é um método de entrega brasileiro.

Este plugin foi desenvolvido sem nenhum incentivo da Jadlog. Nenhum dos desenvolvedores deste plugin possuem vínculos com esta empresa. E note que este plugin foi feito baseado na documentação do Webservice da Jadlog.

Este plugin foi baseado no trabalho de [Claudio Sanches](https://claudiosanches.com/) no [Claudio Sanches - Correios for WooCommerce](https://github.com/claudiosanches/woocommerce-correios).

= Serviços integrados =

Estão integrados os seguintes serviços:

- Entrega nacional:
 - Jadlog .COM
 - Jadlog Cargo
 - Jadlog Corporate
 - Jadlog Econômico
 - Jadlog Expresso
 - Jadlog Package
 - Jadlog Rodoviário

= Instalação: =

Confira o nosso guia de instalação e configuração do Correios na aba [Installation](http://wordpress.org/extend/plugins/datadev-jadlog-for-woocommerce/installation/).

= Compatibilidade =

Requer WooCommerce 3.8 ou posterior para funcionar.

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

- A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/datadev-jadlog-for-woocommerce/faq/).
- Utilizando o nosso [fórum no Github](https://github.com/datadev/datadev-jadlog-for-woocommerce).
- Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/datadev-jadlog-for-woocommerce).

== Installation ==

= Instalação do plugin: =

- Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
- Ative o plugin.

= Requerimentos: =

- [SimpleXML](http://php.net/manual/pt_BR/book.simplexml.php) ativado no PHP (note que já é ativado por padrão no PHP 5).

= Configurações do plugin: =

- Em breve

= Configurações dos produtos =

É necessário configurar o **peso** e **dimensões** de todos os seus produtos, caso você queria que a cotação de frete seja exata.
Note que é possível configurar com produtos do tipo **simples** ou **variável** e não *virtuais* (produtos virtuais são ignorados na hora de cotar o frete).  

== Frequently Asked Questions ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* WooCommerce 4.5.2 ou posterior.
* [SimpleXML](http://php.net/manual/pt_BR/book.simplexml.php) ativado no PHP (note que já é ativado por padrão no PHP 5).
* Adicionar peso e dimensões nos produtos que pretende entregar.

= Quais são os métodos de entrega que o plugin aceita? =

São aceitos os seguintes métodos de entrega nacionais:

- Jadlog .COM
- Jadlog Cargo
- Jadlog Corporate
- Jadlog Econômico
- Jadlog Expresso
- Jadlog Package
- Jadlog Rodoviário

= Onde configuro os métodos de entrega? =

Os métodos de entrega devem ser configurados em "WooCommerce" > "Configurações" > "Entrega" > "Áreas de entrega".

É necessário criar uma área de entrega para o Brasil ou para determinados estados brasileiros e atribuir os métodos de entrega.

= Como é feita a cotação do frete? =

A cotação do frete é feita utilizando o Simulador de Frete através Webservice da Jadlog.

Na cotação do frete é usado o seu CEP de origem, CEP de destino do cliente, junto com as dimensões dos produtos e peso. Desta forma o valor cotado sera o mais próximo possível do real.

= Tem calculadora de frete na página do produto? =

Não tem, simplesmente porque não faz parte do escopo deste plugin.

Escopo deste plugin é prover integração entre o WooCommerce e a Jadlog.

= Este plugin faz alterações na calculadora de frete na página do carrinho ou na de finalização? =

Não, nenhuma alteração é feita, este plugin funcionando esperando o WooCommerce verificar pelos valores de entrega, então é feita uma conexão com a Jadlog e os valores retornados são passados de volta para o WooCommerce apresentar.

Note que não damos suporte para qualquer tipo de personalização na calculadora, simplesmente porque não faz parte do escopo do plugin, caso você queria mudar algo como aparece, deve procurar ajuda com o WooCommerce e não com este plugin.

= Como resolver o erro "Não existe nenhum método de entrega disponível. Por favor, certifique-se de que o seu endereço esta correto ou entre em contato conosco caso você precise de ajuda."? =

Primeiro de tudo, isso não é um erro, isso é uma mensagem padrão do WooCommerce que é exibida quando não é encontrado nenhuma método de entrega.

Mesmo você configurando os métodos de entrega, eles não são exibidos quando a Jadlog retorna mensagem de erro, por exemplo quando a região onde o cliente esta não é coberta pela Jadlog.

Entretanto boa parte das vezes esse tipo de coisa acontece porque os métodos e/ou produtos não foram configurados corretamente.

Aqui uma lista de erros mais comuns:

- Faltando CEP de origem nos métodos configurados.
- CEP de origem inválido.
- Produtos cadastrados sem peso e dimensões
- Peso e dimensões cadastrados de forma incorreta (por exemplo configurando como 1000kg, pensando que seria 1000g, então verifique as configurações de medidas em `WooCommerce > Configurações > Produtos`).

E não se esqueça de verificar o erro ativando a opção de **Log de depuração** nas configurações de cada método de entrega. Imediatamente após ativar o log, basta tentar cotar o frete novamente, fazendo assim o log ser gerado. Você pode acessar todos os logs indo em "WooCommerce" > "Status do sistema" > "Logs".

Dica: Caso apareça no log a mensagem `WP_Error: connect() timed out!` pode acontecer do site da Jadlog ter caído ou o seu servidor estar com pouca memoria.

= Os métodos de entrega da Jadlog não aparecem no carrinho ou durante a finalização? =

As mesmas dicas da sessão acima valem como solução para isto também.

= O valor do frete calculado não bateu com a simulação de frete do site da Jadlog? =

Este plugin utiliza o Webservice da Jadlogpara calcular o frete e quando este tipo de problema acontece geralmente é porque:

1. Foram configuradas de forma errada as opções de peso e dimensões dos produtos na loja.
2. Configurado errado o CEP de origem nos métodos de entrega.

= Ainda esta tendo problemas? =

Se estiver tendo problemas, antes de tudo ative a opção de **Log de depuração** do método que você esta tendo problema e tente novamente cotar o frete, fazendo isso, um arquivo de log é criado e são registradas as respostas do Webservice da Jadlog, leia o arquivo de log, nele é descrito exatamente o que esta acontecendo, tanto o que foi concluindo com sucesso ou não.

Se ainda não foi capaz de solucionar o problema, copie o conteúdo do arquivo de log, cole no [pastebin.com](http://pastebin.com), salve e pegue o link gerado, depois disso abra um tópico informando o seu problema no [fórum de suporte do plugin](https://wordpress.org/support/plugin/woocommerce-correios/#new-post).

= Dúvidas sobre o funcionamento do plugin? =

Em caso de dúvidas, basta abrir um tópico no [fórum de suporte do plugin](https://wordpress.org/support/plugin/datadev-jadlog-for-woocommerce/#new-post). Você será respondido conforme disponibilidade e caso sua dúvida for relacionada com o funcionamento deste plguin.

== Screenshots ==

- Em breve

== Changelog ==

= 1.0.1 - 2020/10/07 = 

- Correção no cálculo do peso

= 1.0.0 - 2019/11/12 =

- Lançamento da versão inicial do plugin

[See changelog for all versions](https://raw.githubusercontent.com/datadev/datadev-jadlog-for-woocommerce/master/CHANGELOG.txt).

== Upgrade Notice ==

= 1.0.0 =

- Lançamento da versão inicial do plugin

