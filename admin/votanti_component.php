<?php
	require '../php/classi.php';
	require '../php/function.php';
	mb_internal_encoding("UTF-8");//header("cache-control: no-store,no-cache,must-revalidate");
	session_start();

	manageStartAMM("../");
	$conn = new mysqli(DB_Credentials::getHost(),DB_Credentials::getUsername(),DB_Credentials::getPassword(),DB_Credentials::getDBname());
	if ($conn->connect_error) {
		$error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #805)";
		//echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
		logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
		//header("location: ./index.php");
		exit();
	}

	$sql = "SELECT inizio_votazione as start,fine_votazione as end FROM informazioni";

	$result = $conn->query($sql);
	if ($result->num_rows <= 0) {
		$error=$_SESSION['AMM_pass_value']= "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore. (errore #12805)";
		//echo "Non e' stato possibile collegarsi al database. Riprova tra qualche minuto o contatta l'amministratore.".$result;
		logError($error,$conn->real_escape_string((isset($_SESSION['AMM_email']) ? $_SESSION['AMM_email'] : $_SERVER['REMOTE_ADDR'])));
		//header("location: ./index.php");
		exit();
	}
	$periodo=$result->fetch_assoc();
	//print_r( $periodo);
	//exit();
	$PREPARAZIONE=($periodo['end']==null || $periodo['start']==null || (date('Y-m-dTH:i')<$periodo['end'] && date('Y-m-dTH:i')<$periodo['start']));
	$DURANTE=(date('Y-m-dTH:i')>=$periodo['start'] && date('Y-m-dTH:i')<=$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
	$TERMINE=(date('Y-m-dTH:i')>$periodo['end'] && $periodo['end']!=null && $periodo['start']!=null);
	if(!$PREPARAZIONE){
?>

	<script>
		function bloccaVotante(bloccare,nome,ID) {
			if (bloccare) {
				document.getElementById("btn_block").innerText = "Blocca";
				document.getElementById("block_vot").innerText = "Bloccare il votante '"+nome+"'? L'eventuale voto non verra' annullato.";
				document.getElementById("tit_blocca").innerText = "Bloccare?";
			}
			else {
				document.getElementById("btn_block").innerText = "Sblocca";
				document.getElementById("block_vot").innerText = "Sbloccare il votante '"+nome+"'?";
				document.getElementById("tit_blocca").innerText = "Sbloccare?";
			}
			document.getElementById("btn_block").href = './block_votante.php?IDvotante='+ID;
			document.getElementById('VotanteBlock').style.display = 'block';
		}
	</script>
<meta charset="UTF-8">

			<?php if($TERMINE){
				?>
					<div class="w3-section" style="text-align: center;" >
						<a class="w3-button w3-section w3-teal w3-ripple" href="./actions/export_votanti.php"> Scarica account</a>

				<?php
				if($_SESSION['LV']>=2){
				?>
						<a class="w3-button w3-section w3-red w3-ripple" href="./actions/export_votanti_e_voti.php"> Scarica account e voti</a>
				<?php
				}
			?>
					</div>
			<?php } ?>
			<header class="w3-container w3-border-bottom">
				<h3>Elenco votanti:</h3>
			</header>
			
			<br />
			<div style="overflow:scroll;overflow-y: hidden;">
				<table class="w3-table w3-striped">
				   
					  <?php
				
						$sql =  " SELECT account_votanti.email,voti.nome_lista,voti.CF_primo_candidato,voti.CF_secondo_candidato,voti.data_voto, account_votanti.tentativi".
								" FROM voti,account_votanti".
								" WHERE voti.email=account_votanti.email".
								" ORDER BY email DESC";
						$result = $conn->query($sql);
						if ($result->num_rows <= 0) {
							echo "NESSUN VOTANTE AL MOMENTO";
							exit();
						}
						else{
						?>
							 <thead>
								<tr class="w3-teal">
									<th class="w3-border-right">Email</th>
									<th class="w3-border-right">Data voto</th>
									<th class="w3-border-right">Tentativi rimasti</th>
									<?php if($_SESSION['LV']>=1 && $DURANTE){?>
									<th class="w3-border-right w3-green">Blocca/Sblocca</th>
									<?php } ?>
									<?php if($_SESSION['LV']>=1){?>
									<th class="w3-border-right w3-green">Annulla voto</th>
									<?php } ?>
								</tr>
							</thead>
						<?php
							for($i=0;$votante=$result->fetch_assoc();$i++){
						?>   
								<tr>
									<td class="w3-border-right"><?php echo $votante['email']; ?></td>
									<td class="w3-border-right"><?php if($votante['data_voto']){echo $votante['data_voto'];}else{echo "NESSUN VOTO";} ?></td>
									<td class="w3-border-right"><?php if($votante['tentativi']==-1){ echo "BLOCCATO"; }else{echo $votante['tentativi'];} ?></td>
									<?php 
									if($_SESSION['LV']>=1 && $DURANTE){?>
										<td class="w3-border-right" style="margin:0;padding:0;"><input type="button" class="<?php if($i%2==0){ echo "w3-white";} ?> w3-button" style="margin:0;width:100%;padding:0;height:35px;" onclick="
																																																		<?php if ($votante['tentativi']>0) {?>
																																																				document.getElementById('btn_block').innerText = 'Blocca';
																																																				document.getElementById('block_vot').innerText = 'Bloccare il votante \''+'<?php echo $votante['email']; ?>'+'\'? L\'eventuale voto non verra\' annullato.';
																																																				document.getElementById('tit_blocca').innerText = 'Bloccare?';
																																																		<?php }
																																																			else { ?>
																																																				document.getElementById('btn_block').innerText = 'Sblocca';
																																																				document.getElementById('block_vot').innerText = 'Sbloccare il votante \''+'<?php echo $votante['email']; ?>'+'\'?';
																																																				document.getElementById('tit_blocca').innerText = 'Sbloccare?';
																																																		<?php } ?>
																																																			document.getElementById('btn_block').onclick = function (){ asyncOpen('./actions/block_votante.php?IDvotante=<?php echo $votante['email']; ?>','./votanti_component.php');};
																																																			document.getElementById('VotanteBlock').style.display = 'block';
																																																		" value="<?php if($votante['tentativi']<=0){echo "Sblocca";}else{echo "Blocca";} ?>" /></td>
									 <?php } ?>
									<?php if($_SESSION['LV']>=1){?>
									<td class="w3-border-right" style="margin:0;padding:0;">
											<?php 
											if($votante['data_voto']){ 
											?>      
												<button class="<?php if($i%2==0){ echo "w3-white";} ?> w3-button" style="margin:0;width:100%;padding:0;height:35px;" onclick="
																																					document.getElementById('annulla_vot').innerText = 'Annullare il voto del votante \'<?php echo $votante['email']; ?>\'?';
																																					document.getElementById('ID_voto').onclick = function (){ asyncOpen('./actions/delete_voto.php?IDaccount=<?php echo $votante['email']; ?>','./votanti_component.php');};
																																					document.getElementById('VotoDelete').style.display = 'block'
																																		" >Annulla</button>
											<?php 
											} 
											?>
										</td>
									<?php 
									} 
									?>
								</tr>
						<?php
							}
						}
					?>
				</table>
			</div>
			<hr>
			
			<br />
			<br />
	<br />
	<div id="VotoDelete" class="w3-modal">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 style="text-align:center">Annullare il voto?</h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('VotoDelete').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/trash_mail.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 id="annulla_vot"></h3>
				<hr />
				<a id="ID_voto" onclick="" class="w3-button w3-teal w3-right" >Rimuovi voto</a>
			</div>
			<br />
		</div>
		<br />
	</div>
	<div id="VotanteBlock" class="w3-modal">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 id="tit_blocca" style="text-align:center"></h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('VotanteBlock').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/trash_mail.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 id="block_vot"></h3>
				<hr />
				<a id="btn_block" class="w3-button w3-teal w3-right" >Blocca</a>
			</div>
			<br />
		</div>
		<br />
	</div>
	<div id="infoUpdates" class="w3-modal" style="display:<?php if(isset($_SESSION['update_info'])){ echo "block"; }else{ echo "none"; } ?>">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 style="text-align:center">Informazioni sull'aggiornamento:</h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('infoUpdates').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 ><?php echo $_SESSION['update_info']; unset($_SESSION['update_info']); ?></h3>
			</div>
			<br />
		</div>
		<br />
	</div>

<?php
}
else{
echo "<br><h5 class=\"w3-text-red\">Ricarica la pagina!<h5><br>";
}
//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>