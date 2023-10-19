
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="./img/SPAAL ico.ico"/>
    <title>Importação de arquivo DDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>
<header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom" style="display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 auto;
}">
    <h1>Importação de extrato</h1>
    <img src="./img/LOGO SPAAL 2 JPG.jpg" alt="Logo da Empresa" style="height: 100px;
    width: auto;">
</header>
<main class="container" style="box-shadow: 0px 0px 4px #ccc; max-width:600px; padding: 20px;">
    <form class="form-login"
          enctype="multipart/form-data"
          action="/"
          method="post">
        <h2>Insira os arquivos</h2>

        <div>
            <label class="campo__etiqueta" for="planilha"></label>
            <input class="form-control form-control-lg"
                   
                   name="extrato[]"
                   accept=".xls"
                   type="file"
                   class="campo__escrita"
                   id="planilha"
                   multiple="multiple" />
        </div>

        <input class="btn btn-secondary" type="submit" value="Enviar" style="margin-top: 15px;"/>
    </form>
    <?php if (isset($_GET['mensagem'])): ?>
        <div class="alert alert-warning" role="alert" style="margin-top: 15px;">
            <?=$_GET['mensagem']?>
            <?php unset($_GET['mensagem'])?>
        </div>
    <?php endif; ?>
    
</main>
</body>