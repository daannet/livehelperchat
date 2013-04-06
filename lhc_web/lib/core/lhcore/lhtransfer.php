<?php

class erLhcoreClassTransfer
{
    public static function getTransferChats($params = array())
    {
       $db = ezcDbInstance::get();
       $currentUser = erLhcoreClassUser::instance();

       if (isset($params['department_transfers']) && $params['department_transfers'] == true) {

	       	$limitation = erLhcoreClassChat::getDepartmentLimitation('lh_transfer');

	       	// Does not have any assigned department
	       	if ($limitation === false) {
	       		return array();
	       	}

	       	$limitationSQL = '';

	       	if ($limitation !== true) {
	       		$limitationSQL = ' AND '.$limitation;
	       	}

	       	// Instances limitation
	       	$limitation = erLhcoreClassChat::getInstanceLimitation('lh_transfer');

	       	// Does not have any assigned instance
	       	if ($limitation === false) {
	       		return array();
	       	}

	       	if ($limitation !== true) {
	       		$limitationSQL = ' AND '.$limitation;
	       	}

	       	$stmt = $db->prepare('SELECT lh_chat.*,lh_transfer.id as transfer_id FROM lh_chat INNER JOIN lh_transfer ON lh_transfer.chat_id = lh_chat.id WHERE transfer_user_id != :transfer_user_id '.$limitationSQL);
	       	$stmt->bindValue( ':transfer_user_id',$currentUser->getUserID());
	       	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	       	$stmt->execute();
	       	$rows = $stmt->fetchAll();
       } else {
	       	$stmt = $db->prepare('SELECT lh_chat.*,lh_transfer.id as transfer_id FROM lh_chat INNER JOIN lh_transfer ON lh_transfer.chat_id = lh_chat.id WHERE lh_transfer.transfer_to_user_id = :user_id');
	       	$stmt->bindValue( ':user_id',$currentUser->getUserID());
	       	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	       	$stmt->execute();
	       	$rows = $stmt->fetchAll();
       }

       return $rows;
   }


   public static function getTransferByChat($chat_id)
   {
       $db = ezcDbInstance::get();

       $stmt = $db->prepare('SELECT * FROM lh_transfer WHERE lh_transfer.chat_id = :chat_id');
       $stmt->bindValue( ':chat_id',$chat_id);
       $stmt->setFetchMode(PDO::FETCH_ASSOC);
       $stmt->execute();
       $rows = $stmt->fetchAll();

       return $rows[0];
   }

   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhtransfer' )
            );
        }
        return self::$persistentSession;
   }

   private static $persistentSession;
}


?>