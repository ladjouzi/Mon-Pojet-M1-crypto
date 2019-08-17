<?php
	session_start();

	//creation du compte
	if(isset($_GET['verify_submit'])&& $_GET['verify_submit'] && isset($_GET['nom']) && isset($_GET['prenom']) && isset($_GET['matricule']) && isset($_GET['groupe']) && preg_match('#^[0-9]{12}$#',$_GET['matricule']) && ($_GET['groupe']==1 || $_GET['groupe']==2))
	{

			try
			{
					$bdd=new PDO('mysql:host=localhost;dbname=projet;charset=utf8','root','');
					$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					//veriefier si le nom existe deja

					$req = $bdd->prepare('SELECT count(*) as count FROM users WHERE nom=?');
					$req->execute(array($_GET['nom']));
					$donnees = $req->fetch();
					$req->closeCursor();

					$existe=$donnees['count'];

					//nous sommes just 6
					$req = $bdd->query('SELECT count(*) as count FROM users');					
					$donnees = $req->fetch();
					$req->closeCursor();

					$nbruples=$donnees['count'];

					//insertion 
					if($existe==0 && ($nbruples < 6))
					{
						$id=substr(sha1(date(DATE_RFC2822)), 0, 50);
						$req = $bdd->prepare('INSERT INTO users VALUES(:id, :nom, :prenom, :matricule, :groupe, :vote)');
						$req->execute(array(
						'id' => md5($id),
						'nom' => $_GET['nom'],
						'prenom' => $_GET['prenom'],
						'matricule' => $_GET['matricule'],
						'groupe' =>$_GET['groupe'],
						'vote' => '0'
						));

						$tab=array("1",$_GET['nom'],$id);
						echo implode("|",$tab);

						$req->closeCursor();

					}
					else echo "0";
				

			}
			catch (Exception $e)
			{
				echo 'Exception -> ';
		    	var_dump($e->getMessage());
			}	
	}
	elseif (isset($_GET['pasencore'])) {     //nbr de place dispo

			try
			{
					$bdd=new PDO('mysql:host=localhost;dbname=projet;charset=utf8','root','');
					$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					$req = $bdd->query('SELECT count(*) as count FROM users');
					$donnees = $req->fetch();

					echo $donnees['count'];

					$req->closeCursor();

			}
			catch (Exception $e)
			{
				echo 'Exception -> ';
		    	var_dump($e->getMessage());
			}	
		
	}	// pour la connexion de l'utilisateur
	elseif (isset($_GET['verify_existing_id']) && $_GET['verify_existing_id'] && isset($_GET['idvalue']) ) {

			try
			{
					$bdd=new PDO('mysql:host=localhost;dbname=projet;charset=utf8','root','');
					$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					$req = $bdd->prepare('SELECT count(*) as count FROM users WHERE id=?');
					$req->execute(array(md5($_GET['idvalue'])));
					$donnees=$req->fetch();

						if($donnees && $donnees['count'] == 1)
						{
							
							$req = $bdd->prepare('SELECT * FROM users WHERE id=?');
							$req->execute(array(md5($_GET['idvalue'])));
							$donnees=$req->fetch();
							if($donnees)
							{
								//ceration de la cle de session
								$_SESSION["sessionID"]=$_GET['idvalue'];
								$tab=array("1",$donnees['nom'],$donnees['prenom'],$donnees['matricule'],$donnees['groupe'],$donnees['vote'],$_SESSION["sessionID"]);
								echo implode("|",$tab);
							}
							else echo "0";
							
						}
						else "0";



					$req->closeCursor();

			}
			catch (Exception $e)
			{
				echo 'Exception -> ';
		    	var_dump($e->getMessage());
			}	
		
	}//remplir la table des vote smpc
	elseif (isset($_GET['submit_vote']) && $_GET['submit_vote'] && isset($_GET['voteValue']) && isset($_GET['nom']) && isset($_GET['sessionID']) && $_GET['sessionID']== $_SESSION['sessionID'])
	{
		$voteValue=explode("|",$_GET['voteValue']);

		$privatePem="-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEA3bEacGFOR6FdTyh9ZXNSB3Fg/Y4IqnOaWmBwOJf+ZdJkllvB
VW84pSElxQ/kuJTT7YzuTA3n/fgTDdcKWhdEje0t854FJXNiXBOKkzpjqDKSMIEh
zSV7H4Y4+dcGeCW7qSCYkX1vCdeUG+wzv2CWvqk1BlskEPsPBkg4DaCGMJFnKBYE
HUffdds07Rpf+uUB1D7KR9TRAVKcadh8riEkBQsj8jRiDcP+lTIboY5RZkbJxMMp
jHU6guE3dB35jB7Rn1yPIF4E/5r3x2e2/wTFPSXL+/+QcpZ9WGdvRXwrQzNbZl0F
vBWaUhY47RsEvfBFBqt2W8UPt++LjpH0dEX6qQIDAQABAoIBAGw7luje2NTn1yJa
Zj4texLfK7cerORq5CTTiHZY3n7fpPQzf8QyNLmJ4WLAS65CGuWTNJQ1BdpcTxEm
t4scaP/Rl+mkcGbEWnURpUoT66umZPEBWntJnT7azN5NOH9YDLKZk2MUli177ndj
bjA0++PBwH2F6anQqJkskRE2Gc2Dhu4pRoAJgDIUhO6P1uccBPny2btTtIK4xYpb
ncDOqPU6/RBxutSjHy7dqw91ALbsy9H4bQ96piYOG+glw3DL/SGMvzXw1iVEAVie
AL/xbDYoiDj1/neurg0a9lquIosPGBgI1KnByvAoSseGHUngEA7FFv0gpeqqZ41u
aNwejrECgYEA8oJ2KItQtJdhonN2NHAEtwyPkKwpuW7P0wBmXNhPeQPYDcoXSes1
NxuouIjmEJhymvmSGkbC6joRoI36K0xWGEuKhC/n1LtNpkyYxhxDtIeF3v0Cdkpm
cIti+lg6qUjwFAw2FnAkU0wL7nsJiXrH7uTjvZwkMwEi04Q8h8vrvFcCgYEA6gYt
ykcLS830geeJb14MEqlINOsG2U4bqvN4RKsfU9vRvnhjqz0Kmqc7SCwE50HlL9wW
eNqtb5xW2ssdeN++oJN2HwQs/qTPEtfJAmBaPDTNevxg7V62nOqlHwPQlnuLvhgO
cjRNy6HwFolcLB3UgKSB3/e7+inJmMYV4M/ZoP8CgYEApiryn7FmKXgY9GE9O40O
DgdTUARHysG9MY1ylme5fOg/YKN69bFvE0WmhFt5mSF8VehwTgTyheAoN+VQ5940
mA0a6pyQs9lryWluvUCcu9SozDR9PWSZcMBxn6xY9an+px3+6b0JhvHyswQncsZo
vK6lxkR5IWHD5T8U4s+9xZ0CgYBtFTw60Sq+xt11v8scEiZmfGmf4P1sRTA4Wwxw
VaNgn1IBCnTK2MUcmV7UoVAXy2tdB6Wh56x4HdOOYKb4NLLSfmnMw1GF0KSFD/gu
F2N4NNSiwwkbG87bDbSx5EFSI3xbuzLgoOiyRmV228gO+EiZPhUbpIoGbXv+rjZ8
d+XlWQKBgFi/qcpKBlTLUC68YaYYEOyWCZeNQdTZqOrHoBkzrqBLw5GP3RACgiq5
Zk/wJ5r1j7xU8fbFN/xTtOp2LSVC66xSiPV6gPvJ/FHgqdA/kzMsAKm8dBSnqWBJ
/pf1oUKegFzBhbLnbnYi8jNbnzTXePnCIAZhNd2WDLOcOyf4LBxb
-----END RSA PRIVATE KEY-----";

$publicPem="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3bEacGFOR6FdTyh9ZXNS
B3Fg/Y4IqnOaWmBwOJf+ZdJkllvBVW84pSElxQ/kuJTT7YzuTA3n/fgTDdcKWhdE
je0t854FJXNiXBOKkzpjqDKSMIEhzSV7H4Y4+dcGeCW7qSCYkX1vCdeUG+wzv2CW
vqk1BlskEPsPBkg4DaCGMJFnKBYEHUffdds07Rpf+uUB1D7KR9TRAVKcadh8riEk
BQsj8jRiDcP+lTIboY5RZkbJxMMpjHU6guE3dB35jB7Rn1yPIF4E/5r3x2e2/wTF
PSXL+/+QcpZ9WGdvRXwrQzNbZl0FvBWaUhY47RsEvfBFBqt2W8UPt++LjpH0dEX6
qQIDAQAB
-----END PUBLIC KEY-----";


		//cryptage des vote 
		$pubKey = openssl_pkey_get_public($publicPem);

		for($i=0;$i<6;$i++)
		{
			openssl_public_encrypt($voteValue[$i],$voteValue[$i],$pubKey);
	
		}
		

		try
			{
					$bdd=new PDO('mysql:host=localhost;dbname=projet;charset=utf8','root','');
					$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			}
			catch (Exception $e)
			{
				echo 'Exception -> ';
		    	var_dump($e->getMessage());
			}


		 switch ($_GET['nom']) {

		    case "ladjouzi":
		      			
		      			$req = $bdd->prepare('UPDATE ladjouzi SET one = :one WHERE nom = :nom');
						$req->execute(array('one' => base64_encode($voteValue[0]),'nom' => 'ladjouzi'));
						$req->execute(array('one' => base64_encode($voteValue[1]),'nom' => 'otmani'));
						$req->execute(array('one' => base64_encode($voteValue[2]),'nom' => 'kebaily'));
						$req->execute(array('one' => base64_encode($voteValue[3]),'nom' => 'ghezzal'));
						$req->execute(array('one' => base64_encode($voteValue[4]),'nom' => 'walker'));
						$req->execute(array('one' => base64_encode($voteValue[5]),'nom' => 'hadjoudj'));

						$req = $bdd->prepare('UPDATE users SET vote = :vote WHERE nom = :nom');
						$req->execute(array('vote' => 1,'nom' => 'ladjouzi'));
						echo "1";
		        break;
		    case "otmani":

		    			$req = $bdd->prepare('UPDATE ladjouzi SET two = :two WHERE nom = :nom');
						$req->execute(array('two' => base64_encode($voteValue[0]),'nom' => 'ladjouzi'));
						$req->execute(array('two' => base64_encode($voteValue[1]),'nom' => 'otmani'));
						$req->execute(array('two' => base64_encode($voteValue[2]),'nom' => 'kebaily'));
						$req->execute(array('two' => base64_encode($voteValue[3]),'nom' => 'ghezzal'));
						$req->execute(array('two' => base64_encode($voteValue[4]),'nom' => 'walker'));
						$req->execute(array('two' => base64_encode($voteValue[5]),'nom' => 'hadjoudj'));

						$req = $bdd->prepare('UPDATE users SET vote = :vote WHERE nom = :nom');
						$req->execute(array('vote' => 1,'nom' => 'otmani'));
		        		echo "1";	
		        break;
		    case "kebaily":

		    			$req = $bdd->prepare('UPDATE ladjouzi SET three = :three WHERE nom = :nom');
						$req->execute(array('three' => base64_encode($voteValue[0]),'nom' => 'ladjouzi'));
						$req->execute(array('three' => base64_encode($voteValue[1]),'nom' => 'otmani'));
						$req->execute(array('three' => base64_encode($voteValue[2]),'nom' => 'kebaily'));
						$req->execute(array('three' => base64_encode($voteValue[3]),'nom' => 'ghezzal'));
						$req->execute(array('three' => base64_encode($voteValue[4]),'nom' => 'walker'));
						$req->execute(array('three' => base64_encode($voteValue[5]),'nom' => 'hadjoudj'));

						$req = $bdd->prepare('UPDATE users SET vote = :vote WHERE nom = :nom');
						$req->execute(array('vote' => 1,'nom' => 'kebaily'));
		        		echo "1";
		        break;
		    case "ghezzal":
		        		$req = $bdd->prepare('UPDATE ladjouzi SET fort = :fort WHERE nom = :nom');
						$req->execute(array('fort' => base64_encode($voteValue[0]),'nom' => 'ladjouzi'));
						$req->execute(array('fort' => base64_encode($voteValue[1]),'nom' => 'otmani'));
						$req->execute(array('fort' => base64_encode($voteValue[2]),'nom' => 'kebaily'));
						$req->execute(array('fort' => base64_encode($voteValue[3]),'nom' => 'ghezzal'));
						$req->execute(array('fort' => base64_encode($voteValue[4]),'nom' => 'walker'));
						$req->execute(array('fort' => base64_encode($voteValue[5]),'nom' => 'hadjoudj'));

						$req = $bdd->prepare('UPDATE users SET vote = :vote WHERE nom = :nom');
						$req->execute(array('vote' => 1,'nom' => 'ghezzal'));
						echo "1";
		        break;
		    case "walker":
		    			$req = $bdd->prepare('UPDATE ladjouzi SET five = :five WHERE nom = :nom');
						$req->execute(array('five' => base64_encode($voteValue[0]),'nom' => 'ladjouzi'));
						$req->execute(array('five' => base64_encode($voteValue[1]),'nom' => 'otmani'));
						$req->execute(array('five' => base64_encode($voteValue[2]),'nom' => 'kebaily'));
						$req->execute(array('five' => base64_encode($voteValue[3]),'nom' => 'ghezzal'));
						$req->execute(array('five' => base64_encode($voteValue[4]),'nom' => 'walker'));
						$req->execute(array('five' => base64_encode($voteValue[5]),'nom' => 'hadjoudj'));

						$req = $bdd->prepare('UPDATE users SET vote = :vote WHERE nom = :nom');
						$req->execute(array('vote' => 1,'nom' => 'walker'));
		       			echo "1";
		        break;
		    case "hadjoudj":

		    			$req = $bdd->prepare('UPDATE ladjouzi SET six = :six WHERE nom = :nom');
						$req->execute(array('six' => base64_encode($voteValue[0]),'nom' => 'ladjouzi'));
						$req->execute(array('six' => base64_encode($voteValue[1]),'nom' => 'otmani'));
						$req->execute(array('six' => base64_encode($voteValue[2]),'nom' => 'kebaily'));
						$req->execute(array('six' => base64_encode($voteValue[3]),'nom' => 'ghezzal'));
						$req->execute(array('six' => base64_encode($voteValue[4]),'nom' => 'walker'));
						$req->execute(array('six' => base64_encode($voteValue[5]),'nom' => 'hadjoudj'));

						$req = $bdd->prepare('UPDATE users SET vote = :vote WHERE nom = :nom');
						$req->execute(array('vote' => 1,'nom' => 'hadjoudj'));
		        		echo "1";
		        break;
		    default:
		        echo "0";
		    }
	
	}//si tous on voté afficher le boutton showresultvote
	elseif(isset($_GET['number_of_votes']) && $_GET['number_of_votes'] && isset($_GET['sessionID']) && $_GET['sessionID']== $_SESSION['sessionID'])
	{

			try
			{
					$bdd=new PDO('mysql:host=localhost;dbname=projet;charset=utf8','root','');
					$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			}
			catch (Exception $e)
			{
				echo 'Exception -> ';
		    	var_dump($e->getMessage());
			}


			$req = $bdd->query('SELECT sum(vote) as totalVotes FROM users');
			$donnees = $req->fetch();
			if($donnees && $donnees['totalVotes']==6) echo "1";
			else echo "0";

					

	}elseif(isset($_GET['give_me_s']) && $_GET['give_me_s'] && isset($_GET['sessionID']) && $_GET['sessionID']== $_SESSION['sessionID']) 
	{	

			try
			{
					$bdd=new PDO('mysql:host=localhost;dbname=projet;charset=utf8','root','');
					$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			}
			catch (Exception $e)
			{
				echo 'Exception -> ';
		    	var_dump($e->getMessage());
			}


			$privatePem="-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEA3bEacGFOR6FdTyh9ZXNSB3Fg/Y4IqnOaWmBwOJf+ZdJkllvB
VW84pSElxQ/kuJTT7YzuTA3n/fgTDdcKWhdEje0t854FJXNiXBOKkzpjqDKSMIEh
zSV7H4Y4+dcGeCW7qSCYkX1vCdeUG+wzv2CWvqk1BlskEPsPBkg4DaCGMJFnKBYE
HUffdds07Rpf+uUB1D7KR9TRAVKcadh8riEkBQsj8jRiDcP+lTIboY5RZkbJxMMp
jHU6guE3dB35jB7Rn1yPIF4E/5r3x2e2/wTFPSXL+/+QcpZ9WGdvRXwrQzNbZl0F
vBWaUhY47RsEvfBFBqt2W8UPt++LjpH0dEX6qQIDAQABAoIBAGw7luje2NTn1yJa
Zj4texLfK7cerORq5CTTiHZY3n7fpPQzf8QyNLmJ4WLAS65CGuWTNJQ1BdpcTxEm
t4scaP/Rl+mkcGbEWnURpUoT66umZPEBWntJnT7azN5NOH9YDLKZk2MUli177ndj
bjA0++PBwH2F6anQqJkskRE2Gc2Dhu4pRoAJgDIUhO6P1uccBPny2btTtIK4xYpb
ncDOqPU6/RBxutSjHy7dqw91ALbsy9H4bQ96piYOG+glw3DL/SGMvzXw1iVEAVie
AL/xbDYoiDj1/neurg0a9lquIosPGBgI1KnByvAoSseGHUngEA7FFv0gpeqqZ41u
aNwejrECgYEA8oJ2KItQtJdhonN2NHAEtwyPkKwpuW7P0wBmXNhPeQPYDcoXSes1
NxuouIjmEJhymvmSGkbC6joRoI36K0xWGEuKhC/n1LtNpkyYxhxDtIeF3v0Cdkpm
cIti+lg6qUjwFAw2FnAkU0wL7nsJiXrH7uTjvZwkMwEi04Q8h8vrvFcCgYEA6gYt
ykcLS830geeJb14MEqlINOsG2U4bqvN4RKsfU9vRvnhjqz0Kmqc7SCwE50HlL9wW
eNqtb5xW2ssdeN++oJN2HwQs/qTPEtfJAmBaPDTNevxg7V62nOqlHwPQlnuLvhgO
cjRNy6HwFolcLB3UgKSB3/e7+inJmMYV4M/ZoP8CgYEApiryn7FmKXgY9GE9O40O
DgdTUARHysG9MY1ylme5fOg/YKN69bFvE0WmhFt5mSF8VehwTgTyheAoN+VQ5940
mA0a6pyQs9lryWluvUCcu9SozDR9PWSZcMBxn6xY9an+px3+6b0JhvHyswQncsZo
vK6lxkR5IWHD5T8U4s+9xZ0CgYBtFTw60Sq+xt11v8scEiZmfGmf4P1sRTA4Wwxw
VaNgn1IBCnTK2MUcmV7UoVAXy2tdB6Wh56x4HdOOYKb4NLLSfmnMw1GF0KSFD/gu
F2N4NNSiwwkbG87bDbSx5EFSI3xbuzLgoOiyRmV228gO+EiZPhUbpIoGbXv+rjZ8
d+XlWQKBgFi/qcpKBlTLUC68YaYYEOyWCZeNQdTZqOrHoBkzrqBLw5GP3RACgiq5
Zk/wJ5r1j7xU8fbFN/xTtOp2LSVC66xSiPV6gPvJ/FHgqdA/kzMsAKm8dBSnqWBJ
/pf1oUKegFzBhbLnbnYi8jNbnzTXePnCIAZhNd2WDLOcOyf4LBxb
-----END RSA PRIVATE KEY-----";

$publicPem="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3bEacGFOR6FdTyh9ZXNS
B3Fg/Y4IqnOaWmBwOJf+ZdJkllvBVW84pSElxQ/kuJTT7YzuTA3n/fgTDdcKWhdE
je0t854FJXNiXBOKkzpjqDKSMIEhzSV7H4Y4+dcGeCW7qSCYkX1vCdeUG+wzv2CW
vqk1BlskEPsPBkg4DaCGMJFnKBYEHUffdds07Rpf+uUB1D7KR9TRAVKcadh8riEk
BQsj8jRiDcP+lTIboY5RZkbJxMMpjHU6guE3dB35jB7Rn1yPIF4E/5r3x2e2/wTF
PSXL+/+QcpZ9WGdvRXwrQzNbZl0FvBWaUhY47RsEvfBFBqt2W8UPt++LjpH0dEX6
qQIDAQAB
-----END PUBLIC KEY-----";

		$privateKey = openssl_pkey_get_private($privatePem);

			//selectioner le nom pour savoir qui pour envoyer les S correspendant
			$req = $bdd->prepare('SELECT nom, count(nom) as n FROM users WHERE id = :id');
			$req->execute(array('id' =>md5($_SESSION['sessionID'])));
			$donnees=$req->fetch();
			if($donnees['n']==1)
			{
			$req = $bdd->query('select one,two,three,fort,five,six from ladjouzi where id=\'1\'');
				$tableau=$req->fetch();
				$a1=0;
				for($i=0;$i<6;$i++)
				{
					openssl_private_decrypt(base64_decode($tableau[$i]),$tableau[$i],$privateKey);
					$a1+=intval($tableau[$i]);	
				}
				

			$req = $bdd->query('select one,two,three,fort,five,six from ladjouzi where id=\'2\'');
				$tableau=$req->fetch();
				$a2=0;
				for($i=0;$i<6;$i++)
				{
					openssl_private_decrypt(base64_decode($tableau[$i]),$tableau[$i],$privateKey);
					$a2+=intval($tableau[$i]);	
				}

			$req = $bdd->query('select one,two,three,fort,five,six from ladjouzi where id=\'3\'');
				$tableau=$req->fetch();
				$a3=0;
				for($i=0;$i<6;$i++)
				{
					openssl_private_decrypt(base64_decode($tableau[$i]),$tableau[$i],$privateKey);
					$a3+=intval($tableau[$i]);	
				}

			$req = $bdd->query('select one,two,three,fort,five,six from ladjouzi where id=\'4\'');
				$tableau=$req->fetch();
				$a4=0;
				for($i=0;$i<6;$i++)
				{
					openssl_private_decrypt(base64_decode($tableau[$i]),$tableau[$i],$privateKey);
					$a4+=intval($tableau[$i]);	
				}

			$req = $bdd->query('select one,two,three,fort,five,six from ladjouzi where id=\'5\'');
				$tableau=$req->fetch();
				$a5=0;
				for($i=0;$i<6;$i++)
				{
					openssl_private_decrypt(base64_decode($tableau[$i]),$tableau[$i],$privateKey);
					$a5+=intval($tableau[$i]);	
				}

			$req = $bdd->query('select one,two,three,fort,five,six from ladjouzi where id=\'6\'');
				$tableau=$req->fetch();
				$a6=0;
				for($i=0;$i<6;$i++)
				{
					openssl_private_decrypt(base64_decode($tableau[$i]),$tableau[$i],$privateKey);
					$a6+=intval($tableau[$i]);	
				}

				
				//envoi de la table finale nom|one ..... five|a1|.....|a6
				$req = $bdd->prepare('SELECT * FROM ladjouzi WHERE nom = :nom');
				$req->execute(array('nom' =>$donnees['nom']));
				$donnees=$req->fetch();
				if($donnees)
				{
					openssl_private_decrypt(base64_decode($donnees['one']),$donnees['one'],$privateKey);
					openssl_private_decrypt(base64_decode($donnees['two']),$donnees['two'],$privateKey);
					openssl_private_decrypt(base64_decode($donnees['three']),$donnees['three'],$privateKey);
					openssl_private_decrypt(base64_decode($donnees['fort']),$donnees['fort'],$privateKey);
					openssl_private_decrypt(base64_decode($donnees["five"]),$donnees["five"],$privateKey);
					openssl_private_decrypt(base64_decode($donnees["six"]),$donnees["six"],$privateKey);


					$tab=array($donnees['one'],$donnees['two'],$donnees['three'],$donnees['fort'],$donnees["five"],$donnees["six"],$a1,$a2,$a3,$a4,$a5,$a6);
					echo implode("|",$tab);
				}
				else echo "2";
			}else echo "3";
	}
	elseif(isset($_GET['testRsa']))
	{
		
		$public="-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC/hWqj4QlMA7wcE2TaefxfUZUJ
iwwvFRzvaVZ4xrQ6ggU1Kdgl+fzbx+A+EB0gS0zISGdhc4n6JxYgjRhS8ZBhSZXm
bgj/AtyPmemm9E67g3SweQjWXeyy5NCSWPPylGtOmGvpJ9UtaRG0k28nZctJho3n
q0JNhbBnGCqLdAcq6wIDAQAB
-----END PUBLIC KEY-----";

		 $private = "-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQC/hWqj4QlMA7wcE2TaefxfUZUJiwwvFRzvaVZ4xrQ6ggU1Kdgl
+fzbx+A+EB0gS0zISGdhc4n6JxYgjRhS8ZBhSZXmbgj/AtyPmemm9E67g3SweQjW
Xeyy5NCSWPPylGtOmGvpJ9UtaRG0k28nZctJho3nq0JNhbBnGCqLdAcq6wIDAQAB
AoGBAK5Mway7lTZ0/7GdhN/AvQoSuUyiG0iOMnNArs3kKQpGYm7r0iddx95Nnate
BuPpI8vy+QMbn6rl/6FIR6bU6cvNh2uhdc/gtD9W/H5QaJyIvmheRVqDiFoRx0rz
QAT0aQUUVSbJVKSs4ILPsgBM3f3JC3iYJRtrPAov8liEX0ShAkEA4jRvqVHh77Mx
OurPMqHHExFnej6v0uOLPCBOnmHKJqsqe1grRrwDRXr6BTXxKy8POjUklvNC6meE
GL9aj6kElQJBANi/dOkSccYKPe0DqV0BAgfaaa5mMSZC5yovV8mzaZk/RIWfuHXm
gkIx98t6MdsXhU/Kzjkt8T9H5umGy8ZgEX8CQQDQb6r4pagAfOj/NkEIkcPj0SS9
oyfWtq+lDswC628f5Jc3ow31lueYzXG2/Xal6S4p37BAnBVr80jomOK0//RlAkB/
BZy3JncEr2XhK78qYPfWsFo0uXDeUmD0qPASpZEiHSDECnlopuD5eB0W4xKqqhsX
SuwWOGVkR3f8rWFobU5zAkEAxGec3RlX7hUu1uwiRth4nMhrv00ql7N8BoQ8Aviu
YB0v2lSnnnmCkUYO98pEYtMn0DU/4eIVrL1iAqitgA6T2g==
-----END RSA PRIVATE KEY-----";

		$res_prv = openssl_get_privatekey($private);
	
		openssl_private_decrypt(base64_decode($_GET['testRsa']), $decrypted, $res_prv, OPENSSL_PKCS1_PADDING);
		echo $decrypted;
		//echo $_SESSION["testRsa"];

		// echo "ddqsd".$_SESSION["testRsa"]."<br>".$_GET["testRsa"];

	}
	//proteger la page du serveur
	else header('Location: page.html');

	

	
	//header('Location: page.html');


?>