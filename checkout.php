<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};






?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Verificar</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <script src="https://www.paypal.com/sdk/js?client-id=AUEf8PKS-w32CVKx4KHR0pX9HvvziydRA09x6RXxr50Ks7KNOdMvY7xCF3GBcfd3a_fECGFToHBr6pZe&currency=USD"></script>

</head>
<body>
<script src="js/chatbot.js"></script>
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>Verificar</h3>
   <p><a href="home.php">INICIO</a> <span> / Verificar</span></p>
</div>

<section class="checkout">

   <h1 class="title">Resumen del pedido</h1>

<form action="" method="post">

   <div class="cart-items">
      <h3>Artículos del carrito</h3>
      <?php
         $grand_total = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
      <p><span class="name"><?= $fetch_cart['name']; ?></span><span class="price">$<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?></span></p>
      <?php
            }
         }else{
            echo '<p class="empty">Tu carrito esta vacío</p>';
         }
      ?>
      <p class="grand-total"><span class="name">Gran total :</span><span class="price">$<?= $grand_total; ?></span></p>
      <a href="cart.php" class="btn">ver carrito</a>
   </div>

   <input type="hidden" name="total_products" value="<?= $total_products; ?>">
   <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
   <input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
   <input type="hidden" name="number" value="<?= $fetch_profile['number'] ?>">
   <input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
   <input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">

   <div class="user-info">
      <h3>Tu información</h3>
      <p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
      <p><i class="fas fa-phone"></i><span><?= $fetch_profile['number'] ?></span></p>
      <p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
      <a href="update_profile.php" class="btn">Actualizar información</a>
      <h3>Dirección de entrega</h3>
      <p><i class="fas fa-map-marker-alt"></i><span><?php if($fetch_profile['address'] == ''){echo 'Por favor ingresa tu dirección';}else{echo $fetch_profile['address'];} ?></span></p>
      <a href="update_address.php" class="btn">Actualizar dirección</a>



      <select name="method" class="box" required>
         <option value="" disabled selected>Seleccione el método de pago--</option>
         <option value="cash on delivery">pago por delivery</option>
         <option value="credit card">Tarjeta de crédito</option>
         <option value="paytm">paytm</option>
         <option value="paypal">PayPal</option>
      </select><!--
      <input type="submit" value="Realizar pedido" class="btn <?php if($fetch_profile['address'] == ''){echo 'disabled';} ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit">
      -->

      <?php
         /*if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = $_POST['address'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      if($address == ''){
         $message[] = '¡Por favor agregue su dirección!';
      }else{
         
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);

         $message[] = '¡Pedido realizado con éxito!';
      }
      
   }else{
      $message[] = 'Tu carrito esta vacío';
   }

/*}*/ 
?>
      <br></br>

      <label>
      <input type="submit" name="submit" value="paypal" checked>
      
      </label>
      
      <div id="paypal-button-container"></div>
      
      <script>
         paypal.Buttons({
            
            style:{
               
               
               shape:   'pill',
               label:   'pay'
            },
            createOrder: function(data, actions){
               return actions.order.create({
                  purchase_units: [{
                     amount:  {
                        value:  <?php echo $grand_total; ?>
                     }
                  }]
               });
            },

            onApprove:  function(data, actions){
               actions.order.capture().then(function (detalles){
                   
                  console.log(detalles);

                  let url = 'captura.php'
                  return fetch(url, {
                     method: 'post',
                     headers: {
                        'content-type': 'application/json'
                     },
                     body: JSON.stringify({
                        detalles: detalles
                     })


                  })

               });
            },

            onCancel:   function(data){
               alert("Pago Cancelado");
               console.log(data);
            }
         }).render('#paypal-button-container');
                  
       
      </script>


   </div>

</form>
   
</section>









<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>