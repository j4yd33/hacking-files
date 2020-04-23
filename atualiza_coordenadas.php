<?php
$apiKey = 'AIzaSyB_Gxok1fpT1SsjdkHin3z0i79RBBNsJK0';
# Crie um token no google geocodig: https://developers.google.com/maps/documentation/geocoding/get-api-key?hl=pt-br
///////////////////////////////////////////////////////
###################NAO MODIFICAR NADA A BAIXO.########################
# - autor: Jordan Detoni

$hostname = "localhost";
$bancodedados = "mkradius";
$usuario = "root";
$senha = "vertrigo";
$mysqli = new mysqli($hostname, $usuario, $senha, $bancodedados);
if ($mysqli->connect_errno) {
    echo "Falha ao conectar: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$sql = "select id, endereco_res, numero_res, bairro_res, cidade_res, estado_res, cep_res, coordenadas from sis_cliente";
$con_sql = $mysqli->query($sql) or die($mysqli->error);
while ($dados_sql = $con_sql->fetch_array()) {
   $id = $dados_sql['id'];
   $logradouro = $dados_sql['endereco_res'];
   $numero = $dados_sql['numero_res'];
   $bairro = $dados_sql['bairro_res'];
   $cidade = $dados_sql['cidade_res'];
   $uf = $dados_sql['estado_res'];
   $cep = $dados_sql['cep_res'];
   $ver_coord = $dados_sql['coordenadas'];
   $resul_coord = strlen($ver_coord);
   if ($resul_coord <= '15'){
   $endA = $numero." + ".$logradouro." + ".$bairro." + ".$cidade." + ".$uf." + ".$cep;
   $endB = str_replace(" ", "+", $endA);
   $address = utf8_encode($endB);
   $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key='.$apiKey.'&address='.urlencode($address).'&sensor=false');
   $geo = json_decode($geo, true);
   if ($geo['status'] == 'OK') {
     $latitude = $geo['results'][0]['geometry']['location']['lat'];
     $longitude = $geo['results'][0]['geometry']['location']['lng'];
     $result_final = $latitude.", ".$longitude;
      $sql = "UPDATE sis_cliente SET coordenadas = '".$result_final. "' where Id = '".$id."'";
      $mysqli->query($sql) or die($mysqli->error);
   }
   else {
      echo "Erro na conexao com o banco de dados.";
      }
   echo @$id."- ".@$result_final;
} else{
 echo "Cadastro já atualizado.";
}
}
?>
