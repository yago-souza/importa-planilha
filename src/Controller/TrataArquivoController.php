<?php

namespace Spaal\Controller;

use PDO;
use PDOException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spaal\Infrastructure\Perscistence\ConnectionCreator;


class TrataArquivoController implements Controller
{
    private function recebeArquivos()
    {
        $diretorioDeArquivos = __DIR__ . '/../../files/';
        $nomeArquivo = $_FILES['extrato']['name'];
        $nomeArquivoTemporario = $_FILES['extrato']['tmp_name'];
        $count = 0;
        $dataAtual = new \DateTime();
        $dataAtual = $dataAtual->format('dmYHis');
        foreach ($nomeArquivo as $arquivo) {
            move_uploaded_file($nomeArquivoTemporario[$count], $diretorioDeArquivos . "fl" . $dataAtual . '.xls');
            $count++;
        }
    }

    private function leArquivo(PDO $PDO)
    {
    
        
        $reader = IOFactory::createReader('Xls');

        $dataAtual = new \DateTime();
        $dataAtual = $dataAtual->format('d/m/y');
        $diretorioDeArquivos = __DIR__ . '/../../files/';
        chdir($diretorioDeArquivos);
        $arquivos = glob("{*.xls,*.XLS}", GLOB_BRACE);#scandir($diretorioDeArquivos);
        $arquivosEnviados = [];
        $arquivosComErro = [];
        $arquivosExistentes = [];
        foreach ($arquivos as $arquivo) {
            $spreadsheet = $reader->load($arquivo);
            $worksheet = $spreadsheet->getSheetByName( 'Sheet0' );
            $maxLin = $worksheet->getHighestRow();
            $maxCol = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($maxCol);

            $agencia = $worksheet->getCellByColumnAndRow(2, 1)->getValue();
            $conta = $worksheet->getCellByColumnAndRow(4, 1)->getValue();
            $saldo = $worksheet->getCellByColumnAndRow(6, 4)->getValue();
            for ($linha = 5 ; $linha <= $maxLin ; ++$linha) {
                $data = $worksheet->getCellByColumnAndRow(1, $linha)->getValue();
                $historico = $worksheet->getCellByColumnAndRow(3, $linha)->getValue();
                $documento = $worksheet->getCellByColumnAndRow(4, $linha)->getValue();
                $valor = $worksheet->getCellByColumnAndRow(5, $linha)->getValue();
                if ($worksheet->getCellByColumnAndRow(6, $linha)->getValue() != '') {
                    $saldo = $worksheet->getCellByColumnAndRow(6, $linha)->getValue();
                }
                #echo "$linha | $agencia | $conta | $data | $historico | $documento | $valor | $saldo </br>";
                #exit();

                try {
                    $sql = "INSERT INTO TST_YSS (
                        LINHA,
                        NOME_ARQUIVO,
                        AGENCIA,
                        CONTA,
                        DATA,
                        HISTORICO,
                        DOCUMENTO,
                        VALOR,
                        SALDO
                        ) 
                        VALUES (
                            '$linha',
                            '$arquivo',
                            '$agencia',
                            '$conta',
                            '$data',
                            '$historico',
                            '$documento',
                            $valor,
                            $saldo
                        )";
                    $statement = $PDO->prepare($sql);
                    $statement->execute();
                } catch (PDOException $erro) {
                    echo $erro->getMessage();
                    array_push($arquivosComErro, $arquivo);
                }
            }

            array_push($arquivosEnviados, $arquivo);

            #move arquivo
            rename( $diretorioDeArquivos . $arquivo, $diretorioDeArquivos . '/tratados/' . $arquivo);
        }

        if (sizeof($arquivosExistentes) >= 1) {
            $arquivosExistentes = implode(', ', $arquivosExistentes);
            $mensagem = "Os arquivos $arquivosExistentes já foram inserido anteriormente.";
        }

        if (sizeof($arquivosEnviados) >= 1) {
            $arquivosEnviados = implode(', ', $arquivosEnviados);
            $mensagem .= "Os arquivos $arquivosEnviados foram enviados.";
        }

        if (sizeof($arquivosComErro) >= 1) {
            $arquivosComErro = implode(', ', $arquivosComErro);
            $mensagem .= "Não foi possivel enviar os arquivos: $arquivosComErro.";
        }

        return $mensagem;
    }

    public function processaRequisicao()
    {
        $PDO = new PDO('oci:SPAAL-SERVGL2/', 'glprod', 'dbaspl');
        $this->recebeArquivos();
        $mensagem = $this->leArquivo($PDO);
        header("Location: /?mensagem=$mensagem");
    }
}