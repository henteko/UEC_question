<?PHP
function sdb_link() {

    $URL = "localhost";
    $USER = "USER_NAME";
    $PASS = "USER_PASS";
    $DB = "UEC_question";
    $link = mysql_connect($URL,$USER,$PASS) or die("MySQLへの接続に失敗しました。");
    $sdb = mysql_select_db($DB,$link) or die("データベースの選択に失敗しました。");
    return $link;
}

function insert($data,$table) {
    $link = sdb_link(); 
    $columns = '';
    $values = '';
    foreach ($data as $column=>$value) {
        if ($columns!='') $columns .= ',';
        $columns .= '`' . $column . '`';
        if ($values!='') $values .= ',';
        $values .= '"' . $value . '"';
    }
    $query = 'INSERT INTO ' . $table . '(' . $columns . ')'. ' VALUES (' . $values . ')';
    $result = mysql_query($query, $link) or die("クエリの送信に失敗しました。<br />SQL:".$query);

    mysql_close($link);
}

function select($table,$where) {
    $link = sdb_link();
    $column = "*";
    $where_clause = "";
    if($where != "") $where_clause = " WHERE " . $where;

    $query = 'SELECT ' . $column . ' FROM ' . $table
        . $where_clause;
    $result = mysql_query($query, $link) or die("クエリの送信に失敗しました。<br />SQL:".$query);
    $row = mysql_fetch_assoc($result);
    mysql_close($link);
    return $row;
}

function update($table,$set,$where) {
    $link = sdb_link();
    $where_clause = "";
    if($where != "") $where_clause = " WHERE " . $where;
    $query = 'UPDATE '.$table.' SET '. $set  . $where_clause;
    $result = mysql_query($query, $link) or die("クエリの送信に失敗しました。<br />SQL:".$query);
    mysql_close($link);
}

function delete_by_session_id($id) {
    $link = sdb_link();
    $table = "Users";
    $where_clause = " WHERE Session_id = '".$id."'";
    $query = 'DELETE FROM ' . $table . $where_clause;
    $result = mysql_query($query, $link) or die("クエリの送信に失敗しました。<br />SQL:".$query);
    mysql_close($link);
}

/*
$table = "Users";
$set = "Q_num = 1";
$where = "id = 1";
update($table,$set,$where);
 */
//$data = array("Session_id"=>"hogehogehoge2","Q_num"=>0,"Q_ok"=>0);
//$table = "Users";
//insert($data,$table);

/*
$id = 2;
$table = "Users";
$where = "id = ". $id;
$result = select($table,$where);
print_r($row);
 */


$Q_DIR = "Q_html/";
$Q_MAX_NUM = 31;
session_start();

$id = session_id();
$table = "Users";
$where = "Session_id = '".$id."'";
$row = select($table,$where);

if($row != null) {
    if(isset($_POST['Twitter'])) {
        //Twitterの処理
        $comment = $row['NAME']."さんの成績は".($row['Q_num'] - 1)."問中:".$row['Q_ok']."問正解です!!/";
        if($row['Q_ok'] > 27) {
            $comment .= "おめでとうございます!!あなたはきっと4年で卒業出来るでしょう!!";
        }else if($row['Q_ok'] > 24) {
            $comment .= "おしい!!あなたはきっと卒業まで5年間かかるでしょう";
        }else if($row['Q_ok'] > 21) {
            $comment .= "おめでとうございます!!あなたは通常の大学生活+2年の延長戦に突入出来るでしょう!!";
        }else if($row['Q_ok'] > 18) {
            $comment .= "おめでとうございます!!あなたは通常の大学生活+3年の延長戦に突入出来るでしょう!!";
        }else {
            $comment .= "あなたは学部を8年以上で卒業するでしょう……踏み入れては行けない領域です";
        }

        $cmd = `ruby rubyで書いたTwitter投稿スクリプトのPath ${comment}`; 
        echo $cmd;
        delete_by_session_id($row['Session_id']);
        require($Q_DIR."tf.html");
    }else if(isset($_POST['comp'])) {
        if($_POST['comp'] == 1) { 
            $ok = $row['Q_ok'];
            $ok++;
            $set = "Q_ok = ".$ok;
            $where = "Session_id = '".$row['Session_id']."'";
            update($table,$set,$where);
            require($Q_DIR.$_POST['id']."O.html");
        }else {
            require($Q_DIR.$_POST['id']."X.html");
        }
    }else if(isset($_POST['NAME'])) {
        $id = $_POST['id'];
        $set = "NAME = '".$_POST['NAME']."'";
        $where = "Session_id = '".$row['Session_id']."'";
        update($table,$set,$where);
        require($Q_DIR.$id.".html");
    }else if(isset($_POST['id'])) {
        $set = "Q_num =".$_POST['id'];
        $where = "Session_id = '".$row['Session_id']."'";
        update($table,$set,$where);
        if($_POST['id'] == $Q_MAX_NUM) {
            require($Q_DIR."f.html");
        }else {
            require($Q_DIR.$_POST['id'].".html");

        }
    }else {
        delete_by_session_id($row['Session_id']);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: BASE_URL/UEC_question/index.php");
    }

}else {
    $data = array("Session_id"=>$id,"Q_num"=>0,"Q_ok"=>0);
    insert($data,$table);
    require("start.html");
}


?>
