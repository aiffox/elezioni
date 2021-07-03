<?php
	require '../php/classi.php';
	require '../php/function.php';
	mb_internal_encoding("UTF-8");header("cache-control: no-store,no-cache,must-revalidate");
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

?>

	

			

			<br />
			<div class="w3-section" style="text-align: center;" >
				<?php if( $_SESSION['LV']>=1 && $PREPARAZIONE ){ ?>
				<a href="./add_lista.php" class="w3-light-grey w3-button w3-border w3-round">Aggiungi lista</a>
				<?php } ?>
				<a href="./actions/export_liste.php" class="w3-light-grey w3-button w3-border w3-round" > Scarica elenco liste</a>
				<a href="./actions/export_cand.php" class="w3-light-grey w3-button w3-border w3-round" > Scarica elenco candidati</a>
			</div>

			<header class="w3-container w3-border-bottom">
				<h3>Elenco delle liste:</h3>
			</header>
			
			<?php 
				
				$sql = "SELECT nome FROM liste";
				$result_liste = $conn->query($sql);
				if ($result_liste->num_rows <= 0) {
					echo "NESSUNA LISTA";
				}
				else{
					while($lista=$result_liste->fetch_assoc()){
						$sql = "SELECT COUNT(*) AS Nvoti FROM voti WHERE voti.nome_lista='".$lista['nome']."'";
						$result_voti = $conn->query($sql);
						if ($result_voti->num_rows <= 0) {
							$voti=array();
							$voti['Nvoti']=0;
						}
						else{
							$voti=$result_voti->fetch_assoc();
						}
				?>
						<br />
						<table class="w3-table w3-bordered w3-striped w3-border">
							<thead>
								<tr class="w3-light-grey">
									<th class="w3-border-right">NOME: <?php echo $lista['nome']; ?></th>
									<?php if(!$PREPARAZIONE){ ?>
										<th class="w3-border-right">VOTI: <?php echo $voti['Nvoti']; ?></th>
									<?php } ?>
									<?php if($_SESSION['LV']>=1 && $PREPARAZIONE){ ?>
										<th class="w3-border-right" style="margin:0;padding:0;"><button class="w3-white w3-button" style="margin:0;width:100%;padding:0;height:35px;" onclick="
																																		document.getElementById('del_list').innerText = 'La lista \'' + '<?php echo $lista['nome']; ?>' + '\' verra\' eliminata insieme a tutti i candidati associati a quest\'ultima.';
																																		document.getElementById('URL_del_list').onclick = function () { asyncOpen('./actions/delete_list.php?IDlista=' + '<?php echo $lista['nome']; ?>','./liste_component.php'); };
																																		document.getElementById('ListDelete').style.display = 'block';
																																	">ELIMINA</button></th>
									<?php } ?>
								</tr>
							</thead>
						</table>
						<div class="w3-panel">
							<table class="w3-table w3-bordered w3-striped w3-border">
								
								<?php  
									$sql = "SELECT nome, cognome, codice_fiscale FROM candidati WHERE candidati.nome_lista='".$lista['nome']."'";
									$result_cand = $conn->query($sql);
									if ($result_cand->num_rows <= 0) {
										echo "<h5 class=\"w3-text-red\">Ancora nessun candidato inserito!</h5>";
									}
									else{
									?>
										<thead>
											<tr class="">
												<th class="w3-border-right">Nome</th>
												<th class="w3-border-right">Cognome</th>
												<?php if(!$PREPARAZIONE){ ?>
													<th class="w3-border-right">Voti attuali</th>
												<?php } ?>
											</tr>
										</thead>
									<?php 
										while($cand=$result_cand->fetch_assoc()){
											$sql = "SELECT COUNT(*) AS Nvoti FROM voti WHERE voti.CF_primo_candidato='".$cand['codice_fiscale']."' OR voti.CF_secondo_candidato='".$cand['codice_fiscale']."'";
											$result_voti_cand = $conn->query($sql);
											if ($result_voti_cand->num_rows <= 0) {
												$voti_cand=array();
												$voti_cand['Nvoti']=0;
											}
											else{
												$voti_cand=$result_voti_cand->fetch_assoc();
											}   
								?>   
											<tr>
												<td class="w3-light-grey w3-button w3-border w3-round"><?php echo $cand['nome']; ?></td>
												<td class="w3-light-grey w3-button w3-border w3-round"><?php echo $cand['cognome']; ?></td>
												<?php if(!$PREPARAZIONE){ ?>
													<td class="w3-light-grey w3-button w3-border w3-round"><?php echo $voti_cand['Nvoti']; ?></td>
												<?php } ?>
												<?php if($_SESSION['LV']>=1 && $PREPARAZIONE){ ?>
													<td class="w3-light-grey w3-button w3-border w3-round" style="margin:0;padding:0;width:40px;"><button class="w3-white w3-button" style="margin:0;width:100%;padding:0;height:35px;" onclick="
																																												document.getElementById('del_cand').innerText = 'Il candidato \'' + '<?php echo $cand['nome']; ?>' + ' ' + '<?php echo $cand['cognome']; ?>' + '\' verra\' eliminato.';
																																												document.getElementById('URL_del_cand').onclick  = function () { asyncOpen('./actions/delete_candidato.php?IDcandidato='+'<?php echo $cand['codice_fiscale']; ?>','./liste_component.php'); };
																																												document.getElementById('CandDelete').style.display = 'block';
																																											">X</button></td>
												<?php } ?>
											</tr>
								<?php
										}
									}
								?>
							</table>
							<br />
							<?php if($_SESSION['LV']>=1 && $PREPARAZIONE ){ ?>
								<form name="frm<?php echo $lista['nome']; ?>">
									<input name="IDlista" type="hidden" value="<?php echo $lista['nome']; ?>"/>
									<table class="w3-table  w3-striped ">
										<tr class="">
											<td ><input name="nome" class="w3-input w3-border w3-round" type="text"  required minlength="12" maxlength="12" placeholder="Nome..."/></td>
											<td ><input name="cognome" class="w3-input w3-border w3-round" type="text"  required minlength="12" maxlength="12" placeholder="Cognome..."/></td>
											<td ><input name="CF" class="w3-input w3-border w3-round" type="text"  required minlength="16" maxlength="16" placeholder="Codice Fiscale..."/></td>
											
										</tr>
										<tr class="w3-white">
											<td ></td>
											<td ><input type="button" class="w3-light-grey w3-button w3-border w3-round" onclick="if (nomeValidate(document.frm<?php echo $lista['nome']; ?>.nome.value) && nomeValidate(document.frm<?php echo $lista['nome']; ?>.cognome.value) && cfValidate(document.frm<?php echo $lista['nome']; ?>.CF.value)) { asyncOpen('./actions/insert_candidati.php?nome=' + document.frm<?php echo $lista['nome']; ?>.nome.value + '&cognome=' + document.frm<?php echo $lista['nome']; ?>.cognome.value + '&IDlista=<?php echo $lista['nome']; ?>' + '&CF=' + document.frm<?php echo $lista['nome']; ?>.CF.value, './liste_component.php'); } else { console.log('errore') }" value="Aggiungi candidato"></td>
											<td ></td>
										</tr>
									</table>
								</form>
							<?php } ?>
						</div>
						<hr>
			<?php
					}
				}
			?>
			
	<br />
	<div id="ListDelete" class="w3-modal">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 style="text-align:center">Eliminare la Lista?</h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('ListDelete').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/trash_mail.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 id="del_list"></h3>
				<hr />
				<a id="URL_del_list" onclick="" class="w3-button w3-teal w3-right" >Elimina</a>
			</div>
			<br />
		</div>
		<br />
	</div>
	<div id="CandDelete" class="w3-modal">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 style="text-align:center">Eliminare il candidato?</h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('CandDelete').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/trash_mail.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 id="del_cand"></h3>
				<hr />
				<a id="URL_del_cand" onclick="" class="w3-button w3-teal w3-right" >Elimina</a>
			</div>
			<br />
		</div>
		<br />
	</div>
	<div id="infoDelete" class="w3-modal" style="display:<?php if(isset($_SESSION['delete_info'])){ echo "block"; }else{ echo "none"; } ?>">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 style="text-align:center">Informazioni sull'eliminazione</h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('infoDelete').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 ><?php echo $_SESSION['delete_info']; unset($_SESSION['delete_info']); ?></h3>
			</div>
			<br />
		</div>
		<br />
	</div>
	<div id="infoInsert" class="w3-modal" style="display:<?php if(isset($_SESSION['insert_info'])){ echo "block"; }else{ echo "none"; } ?>">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 style="text-align:center">Informazioni sull'aggiunta</h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('infoInsert').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 ><?php echo $_SESSION['insert_info']; unset($_SESSION['insert_info']); ?></h3>
			</div>
			<br />
		</div>
		<br />
	</div>
	<div id="info" class="w3-modal" style="display:<?php if(isset($_SESSION['info'])){ echo "block"; }else{ echo "none"; } ?>">
		<div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
			<header class="w3-container w3-teal">
				<h1 style="text-align:center">Informazioni</h1>
			</header>
			<div class="w3-center">
				<br />
				<span onclick="document.getElementById('info').style.display = 'none';" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">x</span>
				<img src="../img/alert.png" alt="Check" style="width:20%" class="w3-circle" />
			</div>
			<div class="w3-container">
				<h3 ><?php echo $_SESSION['info']; unset($_SESSION['info']); ?></h3>
			</div>
			<br />
		</div>
		<br />
	</div>

<?php

//CHIUSURA CONNESSIONE------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

mysqli_close($conn);


?>