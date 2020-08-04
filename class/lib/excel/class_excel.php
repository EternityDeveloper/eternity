<?php
/*
Creditos:

- DzaiaCuck
- dzaiacuck@ig.com.br 
- Desenvolvedor

Description: (lang = pt_br "Brasil") 

Esta classe le(todos) os campos da uma *tabela gerando automaticamente 
um arquivo excel com os dados da *mesma, alinhando/separando por colunas, mantendo
a integridade do banco/tabela, geralmente uso para BKPs e/ou simplesmente trabalhar os dados
no excel. 


Importante lembrar que possui como base a classe "EXCEL GEN" que gera arquivos do excel 
com base em dados fornecidos tabularmente


Licença:
Usa o bagulho...
Use the class without problem... is free




######### EXEMPLO/EXAMPLE (FILE exemplo.php content)
<?
# Brasil ***************************************

//params of the your server-dataBase
// parametros do seu servidor de banco
require("db_config.inc");

// classe (class)
require("mid_excel.class");

//Estanciar
$mid_excel = new MID_SQLPARAExel;

// data to the file(Dados para o arquivo)
$sql = "select * from alunos";

//ex.:
//$mid_excel->mid_sqlparaexcel("DataBaseName", "TABLEname", RECORDSET, "FILEname");
$mid_excel->mid_sqlparaexcel("ESCOLA", "alunos", $sql, "arquivo_alunos");
?>
*/


class  MID_SQLPARAExel extends GeraExcel
    {
    #Variaveis da classe GeraEcel
    var $armazena_dados;    // Armazena dados
    var $nomeDoArquivoXls;  // Nome para o arquivo excel

// define parametros(init)
function mid_sqlparaexcel($banco, $tabela, $sql, $arquivo){

$arquivo = trim($arquivo);

// define nome do arquivo
$this->nomeDoArquivoXls = "MID_".date("His")."_".$arquivo;

// Colsulta

$consReg = mysql_query($sql);
$linReg  = mysql_num_rows($consReg);

$linTable  = mysql_fetch_object($consReg);


// quadro 1, primeira linha, da primeira coluna X/Y
$excel_linha  = 0;
$excel_coluna = 0;

$qtdColunas=0;

	foreach($linTable as  $a => $d){
	   	$vCampox = trim(ucwords(str_replace("_"," ", $a)));
	    $this->MontaConteudo(0, $qtdColunas, $vCampox);		
		$qtdColunas=$qtdColunas+1;
	}
	
// linha em branco
$this->MontaConteudo(1, 0, "   ");
$this->MontaConteudo(1, 1, "   ");

mysql_data_seek($consReg,0);
$objects=mysql_fetch_object($consReg);
// Monta Colunas
//for($excel_coluna = 0; $excel_coluna < $qtdColunas; $excel_coluna++)

$consRegs = mysql_query($sql);
$excel_coluna=0;
$excel_linha=0;
foreach($objects as  $a => $d)
   {
	$nome_coluna=$a;

    for($i=0; $i < $linReg; $i++)
	   {
       //pega registros
	   $valorCelula = mysql_result($consRegs, $i, $nome_coluna);
       
	   //linhas, começa depos do cabecario
	   //$excel_linha = ($i + 2);
	   $excel_linha = ($i + 2);
	  
	   $this->MontaConteudo($excel_linha, $excel_coluna, $valorCelula);
		
	   }// fecha for 'Linhas'
$excel_coluna=$excel_coluna+1;	
$excel_linha=$excel_linha+1;	   
 }// fecha for 'Colunas'


// Cria arquivo

$this->GeraArquivo();

 }// fecha funcao
}// fecha classe



// Gera EXCEL
class  GeraExcel{

// define parametros(init)
function  GeraExcel(){

$this->armazena_dados   = ""; // Armazena dados para imprimir(temporario)
$this->nomeDoArquivoXls = $nomeDoArquivoXls; // Nome do arquivo excel
$this->ExcelStart();
}// fim constructor

     
// Monta cabecario do arquivo(tipo xls)
function ExcelStart(){

//inicio do cabecario do arquivo
$this->armazena_dados = pack( "vvvvvv", 0x809, 0x08, 0x00,0x10, 0x0, 0x0 );
}

// Fim do arquivo excel
function FechaArquivo(){
$this->armazena_dados .= pack( "vv", 0x0A, 0x00);
}


// monta conteudo
function MontaConteudo( $excel_linha, $excel_coluna, $value){

$tamanho = strlen( $value );
$this->armazena_dados .= pack( "v*", 0x0204, 8 + $tamanho, $excel_linha, $excel_coluna, 0x00, $tamanho );
$this->armazena_dados .= $value;
}//Fim, monta Col/Lin

// Gera arquivo(xls)
function GeraArquivo(){

//Fecha arquivo(xls)
$this->FechaArquivo();


header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/octet-stream; name=$this->nomeDoArquivoXls".".xls");
header ( "Content-Disposition: attachment; filename=$this->nomeDoArquivoXls".".xls"); 
header ( "Content-Description: MID Gera excel" );
print  ( $this->armazena_dados);


}// fecha funcao
# Fim da classe que gera excel
}
?>