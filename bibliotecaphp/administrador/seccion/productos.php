<?php include('../template/cabecera.php');?>
<?php 
$txtID=(isset($_POST['txtID']))?$_POST['txtID']:'';
$txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:'';
$txtImagen=(isset($_FILES['txtImagen']['name']))?$_FILES['txtImagen']['name']:'';
$accion=(isset($_POST['accion']))?$_POST['accion']:'';

include('../config/bd.php');

switch($accion){
    case'Agregar':
        $sentenciaSql= $conexion->prepare("INSERT INTO libros (nombre,imagen) VALUES (:nombre,:imagen);");
        $sentenciaSql->bindParam(':nombre',$txtNombre);
       
        $fecha= new DateTime();
        $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";

        $tmpImagen=$_FILES['txtImagen']['tmp_name'];

        if($tmpImagen!=''){
            move_uploaded_file($tmpImagen,'../../img/'.$nombreArchivo);
        }

        $sentenciaSql->bindParam(':imagen',$nombreArchivo);
        $sentenciaSql->execute();
        
        header("location:productos.php");
    break;

    case'Modificar':
        $sentenciaSql= $conexion->prepare("UPDATE libros SET nombre=:nombre WHERE id=:id;");
        $sentenciaSql->bindParam(':nombre',$txtNombre);
        $sentenciaSql->bindParam(':id',$txtID);
        $sentenciaSql->execute();
    
     if($txtImagen!=''){
        $fecha= new DateTime();
        $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
        
        $tmpImagen=$_FILES['txtImagen']['tmp_name'];
        move_uploaded_file($tmpImagen,'../../img/'.$nombreArchivo);

        $sentenciaSql= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id;");
        $sentenciaSql->bindParam(':id',$txtID);
        $sentenciaSql->execute();
        $libro=$sentenciaSql->fetch(PDO::FETCH_LAZY);
       
            if(isset($libro["imagen"])&&($libro["imagen"]!="imagen.jpg")){

                if(file_exists("../../img/".$libro["imagen"])) {
                    unlink("../../img/".$libro["imagen"]);
                }
            }

        $sentenciaSql= $conexion->prepare("UPDATE libros SET imagen=:imagen WHERE id=:id;");
        $sentenciaSql->bindParam(':imagen',$nombreArchivo);
        $sentenciaSql->bindParam(':id',$txtID);
        $sentenciaSql->execute();
    }
    
    header("location:productos.php");
    break;

    case'Cancelar':
       header("location:productos.php");
    break;

    case'Seleccionar':
        $sentenciaSql= $conexion->prepare("SELECT * FROM libros WHERE id=:id;");
        $sentenciaSql->bindParam(':id',$txtID);
        $sentenciaSql->execute();
        $libro=$sentenciaSql->fetch(PDO::FETCH_LAZY);

        $txtNombre= $libro['nombre'];
        $txtImagen= $libro['imagen'];
  
    break;

    case 'Borrar':
       
        $sentenciaSql= $conexion->prepare("SELECT imagen FROM libros WHERE id=:id;");
        $sentenciaSql->bindParam(':id',$txtID);
        $sentenciaSql->execute();
        $libro=$sentenciaSql->fetch(PDO::FETCH_LAZY);
       
            if(isset($libro["imagen"])&&($libro["imagen"]!="imagen.jpg")){

                if(file_exists("../../img/".$libro["imagen"])) {
                    unlink("../../img/".$libro["imagen"]);
                }
            }
       $sentenciaSql= $conexion->prepare("DELETE FROM libros WHERE id=:id;");
        $sentenciaSql->bindParam(':id',$txtID);
        $sentenciaSql->execute();
        
        header("location:productos.php");
        break;

    }

    $sentenciaSql= $conexion->prepare("SELECT * FROM libros;");
    $sentenciaSql->execute();
    $listaLibros=$sentenciaSql->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="col-md-5">
    
<div class="card">
    <div class="card-header">
        Datos de libro:
    </div>    

    <div class="card-body">
    <form method="POST" enctype="multipart/form-data">

<div class="form-grup">
<label for="txtID">ID:</label>
<input type="text" required readonly class="form-control" value="<?php echo $txtID;?>" name="txtID" id="txtID" placeholder="ID"/>
</div>

<div class="form-grup">
<label for="txtNombre">Nombre:</label>
<input type="text" required class="form-control" value="<?php echo $txtNombre;?>" name="txtNombre" id="txtNombre" placeholder="Nombre del libro"/>
</div>

<div class="form-grup">
<label for="txtNombre">Imagen:</label>
<br/>

<?php if($txtImagen!=""){ ?>
    <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen;?>" width="50" alt="">
<?php } ?>

<input type="file" class="form-control"  name="txtImagen" id="txtImagen" placeholder="Nombre del libro">
</div>

<div class="btn-group" role="group" aria-label="">
    <button type="submit" <?php echo($accion=="Seleccionar")?"disabled":"";?> name="accion" value="Agregar" class="btn btn-success">Agregar</button>
    <button type="submit" <?php echo($accion!="Seleccionar")?"disabled":"";?> name="accion" value="Modificar" class="btn btn-info">Modificar</button>
    <button type="submit" <?php echo($accion!="Seleccionar")?"disabled":"";?> name="accion" value="Cancelar" class="btn btn-warning">Cancelar</button>
</div>
</form>

    </div>    
</div>
</div>


<div class="col-md-7">
   <table class="table table-bordered">
       <thead>
           <tr>
               <th>ID</th>
               <th>Nombre</th>
               <th>Imagen</th>
               <th>Acciones</th>
           </tr>
       </thead>
       <tbody>
          <?php foreach ($listaLibros as $libro) {?>
          
            <tr>
               <td><?php echo $libro['id'];?></td>
               <td><?php echo $libro['nombre'];?></td>
               <td>
                   
                <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['imagen'];?>" width="50" alt="">
               
                </td>
               <td>
                
                <form method="post">
                    <input type="hidden" name="txtID" id="txtID" value="<?php echo $libro['id'];?>"/>
                    <input type="submit" name="accion" value="Seleccionar" class="btn btn-info"/>
                    <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>

                </form>
                </td>
            </tr>
        <?php } ?>  
       </tbody>
   </table>
   
</div>




<?php include('../template/pie.php');?>
