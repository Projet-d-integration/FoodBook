<?php 
    session_start(); 
    if(array_key_exists('buttonDeconnecter', $_POST)) {
        session_destroy();
        header('Location: index.php');
    }

    if(empty($_SESSION['idUser']))
    {
        echo '<script>window.location.href = "login.php";</script>';
    }
    
?>

<head>
    <title>Liste d'épicerie Foodbook</title>
    
    <meta charset="utf-8">
    
    <style>
        <?php require 'styles/groceries-list.css'; ?>

        <?php require 'styles/must-have.css'; ?>
        <?php require 'scripts/body-scripts.php'; ?>
        <?php require 'scripts/db.php'; ?>
    </style>
    
    <?php RenderFavicon(); ?>
</head>

<body>
    <div class="header-banner">
        <a href="index.php"><?php echo file_get_contents("utilities/foodbook-logo.svg"); ?></a>
        <div class="banner-title"> Liste d'épicerie </div>
        <div class="svg-wrapper">
            <a href="groceries-list.php" class="svg-button list-button"> <?php echo file_get_contents("utilities/list.svg"); ?> </a>
            <?php 
                if(!empty($_SESSION['idUser'])){
                    echo '<a href="edit-profil.php" class="svg-button login-button"> '.file_get_contents("utilities/account.svg").'</a>';
                    echo '<form method="post"><button type="submit" name="buttonDeconnecter" class="svg-button login-button" value="buttonDeconnecter" />'.file_get_contents("utilities/logout.svg").'</form>';
                }
                else{
                    echo '<a href="login.php" class="svg-button login-button"> '.file_get_contents("utilities/account.svg").'</a>';
                }
            ?>
        </div>
    </div> 

    <div class="wrapper-list">

    <?php
        $tabInfoList = InfoGroceriesList($_SESSION["idUser"]);
        $nb_liste = count($tabInfoList);

        
        if(!($_SERVER['REQUEST_METHOD'] === 'POST'))
        {
            echo"
        <script>
            window.onload = () => { document.getElementById('add-new-grocery-list').style.display = 'block'; }
        </script>";
        
            if($nb_liste <= 0)
            {
                echo '<script>
                window.onload = () => { 
                    document.getElementById("no-list-message").style.display = "block";
                    document.getElementById("add-new-grocery-list").style.display = "block";
                }
            </script>';
            }
            else{
                echo '
                <div class="list-grid">';

                //Afficher toutes les listes
                
                foreach($tabInfoList as $listeEpicerie)
                {
                    $tabIngredList = InfoItemGroceriesList($listeEpicerie[0]);
                    echo "<button type='submit' id='liste-ep-btn' name='btnList' onclick='ShowElementOfList($listeEpicerie[0])' class='list-div' value='$listeEpicerie[0]'> $listeEpicerie[1] <div class='list-div-arrow'>".file_get_contents("utilities/caret.svg")."</div>";
                
                    echo 
                    "<div class='showElementListNone' id='elementList-$listeEpicerie[0]'>
                    <div>Description: $listeEpicerie[2]</div>
                    <div class='btnAddIngredToList' onclick='ShowFormAddIngredient()'>Ajouter un ingrédient</div>";
                    foreach($tabIngredList as $ingredient)
                    {
                        $infoIngredient = SingleIngredientInfo($infoIngredient[3]);
                    }
                    echo"<form method='POST'>
                        <input name='listeEpiDel' type='submit' id='listToDelete-$listeEpicerie[0]' value='Supprimer cette liste' class='btnSupListEp' title='Supprimer la liste ".$listeEpicerie[1]."'>
                        <input type='hidden' name='bruno' value='$listeEpicerie[0]'>
                    </form>
                   
                    </div>";
                    
                }
                echo '</div>';
                
            }
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if(isset($_POST["listeEpiDel"]))
            {
                DeleteGroceriesList($_POST["bruno"]);
                ChangePage("groceries-list.php");
            }

            if(isset($_POST["addGroceryList"]) && !empty($_POST["list-grocery-name"]) && !empty($_POST["description-grocery-list"]))
            {
                $tabInfoList = InfoGroceriesList($_SESSION['idUser']);
                $listAlreadyExists = false;

                foreach($tabInfoList as $liste)
                {
                    if (strtolower($liste[1]) == strtolower($newList)) {
                        $listAlreadyExists = true;
                        break;
                    }
                }
            }

            if(!$listAlreadyExists)
            {
                if($nb_liste == 0)
                {
                    AddGroceriesList($_POST["list-grocery-name"],$_POST["description-grocery-list"],true,$_SESSION["idUser"]);
                    ChangePage("groceries-list.php");
                }
                else{
                    AddGroceriesList($_POST["list-grocery-name"],$_POST["description-grocery-list"],false,$_SESSION["idUser"]);
                    ChangePage("groceries-list.php");
                }
               
            }
            else{
                echo "Vous avez déjà une liste nommée ainsi, elle n'a donc pas été ajouté.";
                echo '<form><div class="item-wrapper"><div class="return-button">'.GenerateButtonTertiary("Retour", "groceries-list.php").'</div></form>';
            }       
        }
         
    ?>
        <div class="neutral_message" id="no-list-message">Vous n'avez pas de liste présentement.</div>
        <div class="add-new-grocery-list" id="add-new-grocery-list" onclick="ShowFormAddList()">Ajouter une liste</div>
        
        <div class="grocery-list-form" id="grocery-list-form">
            <div class="transparent-background">
                <form method="POST" class="form-content">
                    <div class="form-exit" onclick="HideFormAddList()"><?php echo file_get_contents("utilities/x-symbol.svg"); ?></div>
                    <div id="message-nb-liste" class="message-liste">  Vous avez actuellement <?php echo $nb_liste;?>/10 liste d'épicerie.</div>
                    <?php
                        $tabInfoList = InfoGroceriesList($_SESSION['idUser']);
                        if(count($tabInfoList) < 10)
                        {
                            echo '
                            <input type="text" class="input-form-name" name="list-grocery-name" placeholder="Nom de la liste..." maxlength="30">
                            <input type="text" class="input-form-name" name="description-grocery-list" placeholder="Courte description de la liste"> 
                            <input type="submit" class="button button-primary" name="addGroceryList" value="Ajouter la liste">
                            ';
                        }

                    ?>
                </form>
            </div>

        </div>
      
    </div>
        <div class="form-add-ingredient" id="form-add-ingred">
            <div class="transparent-background">
                <form class="form-content">
                <div class="form-exit" onclick="HideFormAddIngredient()"><?php echo file_get_contents("utilities/x-symbol.svg"); ?></div>
                    <legend>Ajout d'un nouvel ingrédient</legend>
                    <input type="text" name="ingred-name" class="input-form-name" placeholder="Nom de l'ingrédient">
                    <input type="number" name="qteIngred" min="0" max="20">
                    <input type="submit" class="button button-primary" value="Ajouter l'ingrédient" name="addIngred">
                </form>
            </div>
           
        </div>

        <?php
        if(!empty($_POST["addIngred"]))
        {
            //AddIngred();
            //ChangePage("groceries-list.php");
        }
        ?>
 
    </div>
    
    

    

    <?php GenerateFooter(); ?>
</body>





<script defer> 

    function ShowFormAddList()
    {
        document.getElementById("grocery-list-form").style.display = "block";
    }

    function HideFormAddList()
    {
        document.getElementById("grocery-list-form").style.display = "none";
    }

    function ShowFormAddIngredient()
    {
        document.getElementById("form-add-ingred").style.display = "block";
    }

    function HideFormAddIngredient()
    {
        document.getElementById("form-add-ingred").style.display = "none";
    }
    function ShowElementOfList(idListe)
    {
        if(document.getElementById("elementList-" + idListe).classList.contains("showElementListNone"))
        {
            document.getElementById("elementList-" + idListe).classList.remove("showElementListNone");
            document.getElementById("elementList-" + idListe).classList.add("showElementListFlex");
        }
        else if(document.getElementById("elementList-" + idListe).classList.contains("showElementListFlex"))
        {
            document.getElementById("elementList-" + idListe).classList.remove("showElementListFlex");
            document.getElementById("elementList-" + idListe).classList.add("showElementListNone");
        }
        
    }
</script>
