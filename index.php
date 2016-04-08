<?php
/*------------------------------ �û����ÿ�ʼ ------------------------------*/
$PW	= 'waipd9si';		// ��������
$TT	= '������ϵͳ1.2';		// ҳ�����
$NPP	= 10;			// ÿҳ��ʾ�ļ�����
$TF	= array('apk','txt', 'doc', 'xls', 'ppt', 'htm', 'html', 'mht', 'rar', 'zip', 'jpg', 'gif', 'png', 'bmp');	// �������ص��ļ�����
$VF	= array('index.php');	// ��������ʾ�����ص��ļ�����
$TD	= array('.');		// Ҫ��ʾ���ļ���
$TDN	= array('��Դ');		// �ļ��е����������ϱ߶�Ӧ
$TDP	= array('');		// �ļ��еķ������룬���ϱ߶�Ӧ������Ҫ����������ļ��о�����Ϊ��
$CS	= false;			// ��ʾʱ�Ƿ�ȥ���ļ����ĺ�׺��trueΪȥ����׺��falseΪ��ȥ����׺
$UAF	= false; 			// �Ƿ������ϴ��κ����͵��ļ���trueΪ�����ϴ��κ��ļ���falseֻ���ϴ��涨�ĺ�׺�ļ�
/*------------------------------ �û����ý��� ------------------------------*/

/*------------------------------ DataDownload�࿪ʼ ------------------------------*/
class DataDownload {
	// ��������
	var $passWord;

	// ҳ�����
	var $title;

	// ÿҳ��ʾ�ļ���
	var $numPerPage;

	//���飬�������ص��ļ����ͣ�Сд��ĸ
	var $tarFiles = array();

	//���飬��������ʾ�����ص��ļ�����
	var $voidFiles = array();

	// ���飬Ҫ��ʾ���ļ���
	var $tarDirs = array();

	// ���飬�ļ��е����������ϱߵ������Ӧ
	var $tarDirsName = array();

	// ���飬�ļ��еķ������룬���ϱߵ������Ӧ������Ҫ����������ļ��о�����Ϊ��
	var $tarDirsPwd = array();

	// ���ʱȥ���ļ�����׺��true or false
	var $cutSuffix;
	
	// �Ƿ������ϴ��κ����͵��ļ������Ϊfalse����ֻ���ϴ��涨�ĺ�׺�ļ�
	var $upAnyFile;

	// ���飬���Ŀ���ļ����ļ���
	var $files = array();

	// ���飬���Ŀ¼������
	var $dirs = array();

	// ���飬����ļ��б�
	var $fileDown = array();

	// ��Ŵ����ļ���Ŀ
	var $fileNum;
	
	// ���飬��ҳ��Ϣ
	var $pages = array();

	// ������ļ�����
	var $outputFiles =array();

	// ������ļ���Ŀ
	var $opFilesNum;

	// Ŀ¼��
	var $dir;
	
	// Ŀ¼ID
	var $dirId;

	// �����ж��Ƿ���Ҫ����
	var $needsPwd = false;

	// �����ʽ���ļ�
	var $CSSFile;

	// ��ǰ�汾
	var $Version = 'Version 1.3';
	// �ļ�����
	var $tarFilesType = array(
		'apk'		=> '��׿Ӧ��',
		'txt'		=> 'txt�ı�',
		'doc'		=> 'word�ĵ�',
		'docx'	 	=> 'word�ĵ�',
		'ppt'		=> '�õ�Ƭ',
		'xls'		=> 'Excel�ĵ�',
		'mdb'	 	=> '���ݿ�',
		'rar'		=> 'RAR�ĵ�',
		'zip'		=> 'ZIP�ĵ�',
		'jpg'		=> 'JPGͼƬ',
		'gif'		=> 'GIFͼƬ',
		'png'		=> 'PNGͼƬ',
		'bmp'		=> 'BMPͼƬ',
		'mp3'	 	=> 'MP3��Ƶ',
		'wma'		=> 'WMA��Ƶ',
		'rm'		=> 'RM��Ƶ',
		'wmv'		=> 'WMV��Ƶ',
		'avi'		=> 'AVI��Ƶ',
		'3pg'		=> '3GP��Ƶ',
		'htm'		=> 'WEB�ĵ�',
		'html'	 	=> 'WEB�ĵ�',
		'mht'		=> 'WEB�ĵ�',
		'css'		=> '��ʽ��',
		'php'		=> 'PHP�ļ�',
		'asp'		=> 'ASP�ļ�',
		'aspx'		=> 'ASP.NET�ļ�',
		'jsp'		=> 'JSP�ļ�',
		'chm'		=> 'CHM�ĵ�',
		'exe'		=> 'EXE�ļ�',
		);

	// ��ȡĿ¼ǰ�Ĳ�������ʼ�����Ŀ¼
	function checkDir() {
		$this->dirId= (int) $_GET['dir'];
		$this->dir = isset($this->tarDirs[$this->dirId]) ? $this->tarDirs[$this->dirId] : $this->tarDirs[0];
		if (substr($this->dir, -1) != '/') {
			$this->dir .= '/';
		}
		if (!is_dir($this->dir)) {
			$this->dir = './';
		}
		if ($this->tarDirsPwd[$this->dirId] != '') {
			$this->needsPwd = true;
		}
	}
	
	// ��ȡָ��Ŀ¼�ڵ��ļ���Ŀ¼�����ظ�$files��$dirs
	function readData() {
		$hd = opendir(rawurldecode($this->dir));
		while (false !== ($file = readdir($hd))) {
			if (is_dir($file) && '.' != $file && '..' != $file) {
				$this->dirs[] = rawurlencode($file);
			}
			if (in_array($this->getSuffix($file), $this->tarFiles) || count($this->tarFiles) ==0) {
				if (!in_array($file, $this->voidFiles) && !is_dir($file)) {
					$this->files[] = $file;
				}
			}
		}
	}

	// �����¼��֤
	function loginDir() {
		$logindir = (int) $_POST['logindir'];
		if ($logindir == -1) {
			if ($this->passWord == $_POST['password']) {
				$_SESSION['admin'] = true;
				header("Location: ?adminlogin");
			} else {
				header("Location: ?adminlogin");
			}
		}
		elseif ($this->tarDirsPwd[$logindir] != '') {
			if ($this->tarDirsPwd[$logindir] == $_POST['password']) {
				$_SESSION['Dir'.$logindir] = $_POST['password'];
			}
			header("Location: ?dir=".$logindir);
		}
	}

	// ����Աע������
	function adminLoginout() {
		$_SESSION['admin'] = false;
		header("Location: ?adminlogin");
	}

	// �����ļ����ĺ�׺����תΪСд��ĸ
	function getSuffix($fileName) {
		$pos = strrpos($fileName, '.');
		$suffix = strtolower(substr($fileName, $pos + 1, strlen($fileName) - $pos - 1));
		return $suffix;
	}

	// ���غ�׺����
	function recSuffix($suffix) {
		return 
		(isset($this->tarFilesType[$suffix]))
		? $this->tarFilesType[$suffix]
		: $suffix;
	}
	
	// ȡ�ý�ȥ��׺���ļ���
	function getfName($fileName) {
		$pos = strrpos($fileName, '.');
		return substr($fileName, 0, $pos);
	}

	// ȡ��Ŀ���ļ��Ļ�����Ϣ
	function getFileInfo() {
		$this->fileNum = sizeof($this->files);
		if ($this->fileNum == 0) {
			return false;
		}
		for ($i = 0; $i < $this->fileNum; $i++) {
			$this->fileDown['name'][$i] = $this->files[$i];
			$this->fileDown['size'][$i] = filesize($this->dir.$this->files[$i]);
			$this->fileDown['date'][$i] = filemtime($this->dir.$this->files[$i]);
			$this->fileDown['suffix'][$i] = $this->getSuffix($this->files[$i]);
		}
	}

	// �õ�ָ���ļ��Ĵ�С
	function getfSize($bytes) {
		$bytes = $bytes / 1024;
		$bytes > 1024 ? $size = number_format($bytes / 1024, 2).'mb' : $size = ceil($bytes).'kb';
		if ($bytes < 1024) {
			$size = ceil($bytes).'kb';
		} elseif ($bytes >= 1024 && $bytes < 1024 * 10) {
			$size = number_format($bytes / 1024, 2).'mb';
		} elseif ($bytes >= 1024 * 10 && $bytes < 1024 * 100) {
			$size = number_format($bytes / 1024, 1).'mb';
		} else {
			$size = ceil($bytes / 1024).'mb';
		}
		return $size; 
	}

	// ��ʽ��Ŀ���ļ��Ļ�����Ϣ
	function fmtFiles() {
		for($i = 0; $i < $this->opFilesNum; $i++) {
			$this->outputFiles[$i]['Fullname'] = $this->fileDown['name'][$i];
			$this->outputFiles[$i]['name'] = $this->cutSuffix ? $this->getfName($this->fileDown['name'][$i]) : $this->fileDown['name'][$i];
			$this->outputFiles[$i]['size'] = $this->getfSize($this->fileDown['size'][$i]);
			$this->outputFiles[$i]['date'] =  date('y��m��d��', $this->fileDown['date'][$i]);
			$this->outputFiles[$i]['suffix'] =  $this->recSuffix($this->fileDown['suffix'][$i]);
		}
	}

	// ���ļ���ָ����ʽ����
	function sortArray($order, $type = 'date') {
		$types = array('date', 'size', 'suffix', 'name');
		if (in_array($type, $types)) {
			$types[array_search($type, $types)] = $types[0];
			$types[0] = $type;
		}
		($order == 'asc')
			? array_multisort($this->fileDown[$types[0]], SORT_ASC,
				$this->fileDown[$types[1]], SORT_ASC,
				$this->fileDown[$types[2]], SORT_ASC,
				$this->fileDown[$types[3]], SORT_ASC)
			: array_multisort($this->fileDown[$types[0]], SORT_DESC,
				$this->fileDown[$types[1]], SORT_DESC,
				$this->fileDown[$types[2]], SORT_DESC,
				$this->fileDown[$types[3]], SORT_DESC);
	}

	// �����ҳ��Ϣ
	function getPageInfo($page) {
		$this->pages['max'] = (int) ceil($this->fileNum / $this->numPerPage);
		$this->pages['now'] = $page;
		if ((int) $page <= 0) {
			$this->pages['now'] = 1;
		}
		if ((int) $page > $this->pages['max']) {
			$this->pages['now'] = $this->pages['max'];
		}
		$this->pages['pre'] = $this->pages['now'] == 1 ? '' : $this->pages['now'] - 1;
		$this->pages['next'] = $this->pages['now'] == $this->pages['max'] ? '' : $this->pages['now'] + 1;
		$this->cutArray();
	}

	// ��ȡ�������
	function cutArray() {
		$start = ($this->pages['now'] - 1) * $this->numPerPage;
		$this->fileDown['name'] = array_slice($this->fileDown['name'], $start, $this->numPerPage);
		$this->fileDown['size'] = array_slice($this->fileDown['size'], $start, $this->numPerPage);
		$this->fileDown['date'] = array_slice($this->fileDown['date'], $start, $this->numPerPage);
		$this->fileDown['suffix'] = array_slice($this->fileDown['suffix'], $start, $this->numPerPage);
		$this->opFilesNum = sizeof($this->fileDown['name']);
	}

	// �����ļ�
	function downloadFile($filename, $dirid) {
		if (strstr('/', $filename)) $filename = substr($filename, strrpos($filename));
		$filename = str_replace('/', '', $filename);
		$filename = str_replace('\\', '', $filename);
		if (!isset($this->tarDirs[$dirid])) {
			echo '<div style=\'color: red;text-align: center;\'>û�д�Ŀ¼!</div>';
			return false;
		}
		if (substr(str_replace('\\', '/', $this->tarDirs[$dirid]), -1) != '/') {
			$this->tarDirs[$dirid] .= '/';
		}
		$name = $filename;
		$filename = $this->tarDirs[$dirid] . $filename;
		if (in_array($name, $this->voidFiles)) {
			echo '<div style=\'color: red;text-align: center;\'>���������ص��ļ�!</div>';
			return false;
		}
		else if (!file_exists($filename) || !in_array($this->getSuffix($filename), $this->tarFiles)) {
			echo '<div style=\'color: red;text-align: center;\'>�����ڵ��ļ�!</div>';
			return false;
		}
		else {
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$name.'"');
			readfile($filename);
		}
	}

	// ɾ���ļ�
	function deleteFile($filename, $dirid) {
		if ($_SESSION['admin'] != true) {
			header("Location: ?");
			return false;
		}
		if (!isset($this->tarDirs[$dirid])) {
			echo "<script type='text/javascript'>alert('û�д�Ŀ¼��');window.close();</script>";
			return false;
		}
		if (strstr('/', $filename)) $filename = substr($filename, strrpos($filename));
		$filename = str_replace('/', '', $filename);
		$filename = str_replace('\\', '', $filename);
		if (substr(str_replace('\\', '/', $this->tarDirs[$dirid]), -1) != '/') {
			$this->tarDirs[$dirid] .= '/';
		}
		$name = $filename;
		$filename = $this->tarDirs[$dirid] . $filename;
		if (!file_exists($filename)) {
			echo "<script type='text/javascript'>alert('�ļ������ڣ�');window.close();</script>";
			return false;
		}
		if (in_array($name, $this->voidFiles)) {
			echo "<script type='text/javascript'>alert('������ɾ�����ļ���');window.close();</script>";
			return false;
		}
		if (@unlink($filename)) {
			echo "<script type='text/javascript'>alert('�ļ� ��{$name}�� ɾ���ɹ���');opener.window.history.go(0);window.close();</script>";
		} else {
			echo "<script type='text/javascript'>alert('�ļ� ��{$name}�� ɾ��ʧ�ܣ�û��Ȩ�ޣ�');window.close();</script>";
		}
	}

	// �ϴ��ļ�
	function uploadFile() {
		if ($_SESSION['admin']) {
			$this->dir = isset($this->tarDirs[$_POST['dirid']]) ? $this->tarDirs[$_POST['dirid']] : $this->tarDirs[0];
			if (substr($this->dir, -1) != '/') {
					$this->dir .= '/';
			}
			if (!is_dir($this->dir)) {
					$this->dir = './';
			}
			if ($_FILES['file']['error'] == 0) {
					if (!$this->upAnyFile && in_array($this->getSuffix($_FILES['file']['name']), $this->tarFiles) == false && count($this->tarFiles) != 0) {
						echo "<script type='text/javascript'>alert('�ļ� ��".$_FILES['file']['name']."�� �ϴ�ʧ�ܣ��Ƿ����ļ����ͣ�');window.location='?dir=".$_POST['dirid']."'</script>";
						return false;
					}
					if (file_exists($this->dir.$_FILES['file']['name'])) {
						echo "<script type='text/javascript'>alert('�ļ� ��".$_FILES['file']['name']."�� �ϴ�ʧ�ܣ�ԭ�ļ��Ѿ����ڣ�');window.location='?dir=".$_POST['dirid']."'</script>";
						return false;
					}
					if (copy($_FILES['file']['tmp_name'], $this->dir.$_FILES['file']['name'])) {
						@chmod($this->dir.$_FILES['file']['name'], 0777);
						echo "<script type='text/javascript'>alert('�ļ� ��".$_FILES['file']['name']."�� �ϴ��ɹ���');window.location='?dir=".$_POST['dirid']."'</script>";
					} else {
						echo "<script type='text/javascript'>alert('�ļ� ��".$_FILES['file']['name']."�� �ϴ�ʧ�ܣ�����ԭ��Ŀ¼������Ȩ�޲��㡣');window.location='?dir=".$_POST['dirid']."'</script>";
						return false;
					}
			} else {
						echo "<script type='text/javascript'>\n";
						echo "alert('�ļ��ϴ�ʧ��\\n";
						echo "����ԭ��:\\n";
						echo "1.�ϴ��ļ���С��������\\n";
						echo "2.Ŀ¼�Ĳ���Ȩ�޲���\\n";
						echo "3.�ϴ�����һ�����ļ�\\n";
						echo "���صĴ������:".$_FILES['file']['error']."');";
						echo "window.location='?dir=".$_POST['dirid']."'</script>";
						return false;
			}
		}
	}

	// �����ʽ��
	function showCSS() {
		if (file_exists($this->CSSFile)) {
			echo "<link rel='stylesheet' style='text/css' href='".$this->CSSFile."' />";
		} else {
		print('<style type="text/css">
<!--
body {
	min-height: 100%;
}
#title {
	text-indent: 20px;
	margin-bottom: 3px;
	padding-bottom: 2px;
	color: #999999;
	font-size: 14px;
}
#dirs {
	width: 180px;
	float: left;
}
#dirs ul {
	margin: 0;
	padding: 0;
	border: 1px solid  #CECFDE;
}
#dirs ul li {
	margin: 0;
	padding: 0;
	list-style-type: none;
}
#dirs ul li a {
	display: block;
	font-weight: 700;
	text-indent: 10px;
	width: 100%;
	border-top: 1px dashed #AAAAAA;
	background: #EEEEEE;
}
#dirs ul li a:hover {
	letter-spacing: 1px;
	color: #000000;
}
#uldir {
	text-indent: 5px;
	font-weight: 700;
}
#box {
	margin: 0 auto;
	width: 860px;
	text-align: left;
}
#box a {
	padding-top: 3px;
	padding-bottom: 3px;
	color: #666666;
}
#files {
	float: right;
}
#contain {
	font-size: 14px;
	border: 1px solid #CECFDE;
	padding: 3px;
	width: 660px;
	margin: auto;
}
#contain ul {
	overflow: hidden;
	display: inline-block;
	margin: 0;
	padding: 0;
	height:100%;
}
#contain li {
	float: left;
	overflow: hidden;
	text-indent: 12px;
	list-style-type: none;
	line-height: 160%;
}
#contain #c_title li {
	font-weight: 700;
}
.t1  {
	width: 316px;
	margin-right: 1px;
	voice-family:"\"}\"";
	margin-right: 3px;
}
.t2  {
	width: 99px;
	margin-right: 1px;
	voice-family:"\"}\"";
	margin-right: 3px;
}
.t3  {
	width: 99px;
	margin-right: 1px;
	voice-family:"\"}\"";
	margin-right: 3px;
}
.t4  {
	width: 134px;
	margin-right: 0;
}
#contain #c_title .t1, #contain #c_title .t2, #contain #c_title .t3, #contain #c_title .t4 {
	text-align: center;
	text-indent: 0;
	padding: 0;
	background-color: #DEDBD6;
	border-right: 1px solid #9C9E9C;
	border-bottom: 1px solid #9C9E9C;
	margin-right: 1px;
	voice-family:"\"}\"";
	margin-right: 2px;
}
#contain #c_title .t4 {
	margin-right: 0;
}
#contain .u1 li {
	background-color: #F7F7F7;
}
#contain .u2 li{
	background-color: #FFFBFF;
}
#contain .u3 {
	width: 657px;
	cursor: pointer;
	color: #666666;
	border-top: 1px solid #999999;
	border-bottom: 1px solid #999999;
	background-color: #CCCCCC;
}
#topbar {
	margin: auto;
	padding: 5px;
	width: 656px;
	background: #EAEAEA;
	border: 1px solid #CECFDE;
	margin-bottom: 5px;
}
#logined {
	margin: auto;
	padding: 5px;
	width: 656px;
	color: #666;
	border: 1px solid #CECFDE;
}
#topbar select {
	width: 120px;
	font-size: 12px;
	font-family: Verdana, "Times New Roman", Times, serif;
}
#topbar span {
	color: #666666;
	font-size: 12px;
	font-weight: 700;
	margin-right: 10px;
	text-decoration: underline;
}
#topbar span#tip {
	color: #DD0000;
	font-size: 12px;
	font-weight: 700;
	text-decoration: none;
}
#bottombar {
	text-align: center;
	margin: auto;
	margin-top: 5px;
	padding: 5px;
	width: 656px;
	background: #EAEAEA;
	border: 1px solid #CECFDE;
}
#bottombar select {
	width: 80px;
	font-size: 12px;
	font-family: Verdana, "Times New Roman", Times, serif;
}
#bottombar span {
	color: #666666;
	font-size: 12px;
	font-weight: 700;
}
#bottombar a {
	text-decoration: none;
	color: #DD0000;
}
#copyright {
	clear: both;
	margin: 0 auto;
	padding-top: 5px;
	padding-left: 180px;
	width: 680px;
	text-align: center;
	color: #666666;
}
#copyright a {
	color: #666666;
	text-decoration: none;
}
#copyright a:hover {
	color: #DD0000;
}
#login {
	border: 1px solid #CECFDE;
	padding-left: 3px;
	padding-top: 10px;
	padding-right: 3px;
	padding-bottom: 10px;
	width: 660px;
	margin: auto;
}
#login form {
	margin: 0;
	padding: 0;
}
#login input.pwd{
	width: 80px;
	height: 12px;
	color: #999999;
	border: 1px dashed #999999;
}
#login input.sbmit{
	width: 80px;
	height: 18px;
	font-family: Verdana, Times, serif;
	background: #FFFFFF;
	border: 1px solid #AAAAAA;
}
#sorry {
	text-align: center;
	color: #DD0000;
	height: 30px;
}
#adminli a {
	color: #999999;
}
#upload form {
	float: right;
	margin: 0;
	padding: 0;
	width: 300px;
}
#upload form input {
	border: 1px solid #999999;
}
#upload form input.file {
	color: #DD0000;
}
.clearfloat {
	clear:both;
	height:0;
	font-size: 1px;
	line-height: 0px;
}
-->
</style>
');
		}
	}

	// ���Ŀ¼�б�
	function showDirs() {
		echo "<div id='dirs'>\n";
		echo "<ul>\n";
		echo "<li id='uldir'>��ѡ��Ŀ¼</li>\n";
		$dir_num = count($this->tarDirs);
		for ($i = 0; $i < $dir_num; $i++) {
			$dir = $this->tarDirsName[$i] ? $this->tarDirsName[$i] : $this->tarDirs[$i];
			echo "<li><a href='?dir=".$i."' title=' ���� ".$dir." '>".$dir."</a></li>\n";
		}
		echo "<li id='adminli'><a href='?adminlogin'>";
		echo $_SESSION['admin'] ? "{����Աҳ��}" : "�����¼";
		echo "</a></li>\n";
		echo "</ul>\n";
		echo "</div>\n";
	}

	// ����ļ��б�
	function showFiles() {
		echo "<div id='contain'>\n";
		echo "<ul id='c_title'>\n";
		echo "<li class=\"t1\">����</li>\n";
		echo "<li class=\"t2\">����</li>\n";
		echo "<li class=\"t3\">��С</li>\n";
		echo "<li class=\"t4\">����</li>\n";
		echo "</ul>\n";
		if ($this->opFilesNum != 0) {
			for ($i = 0; $i < $this->opFilesNum; $i ++) {
				$c = $i % 2 == 0 ? 'u1' : 'u2';
				$clickN = 'javascript:window.location=\'?dir='.$this->dirId.'&amp;filename='.$this->outputFiles[$i]['Fullname'].'\'';
				$clickA = 'javascript:if(event.ctrlKey){return deletef(\''.$this->outputFiles[$i]['Fullname'].'\',\''.rawurlencode($this->outputFiles[$i]['Fullname']).'&amp;dir='.$this->dirId.'\');}else{window.location=\'?dir='.$this->dirId.'&amp;filename='.rawurlencode($this->outputFiles[$i]['Fullname']).'\'}';
				$click = $_SESSION['admin'] ? $clickA : $clickN;
				echo '<ul class="'.$c.'" onmouseover="this.className=\'u3\';" onmouseout="this.className=\''.$c.'\'" title=" ������� '.$this->outputFiles[$i]['name'].' " onclick="'.$click.'">';
				echo '<li class="t1">'.$this->outputFiles[$i]['name'].'</li>';
				echo '<li class="t2">'.$this->outputFiles[$i]['suffix'].'</li>';
				echo '<li class="t3">'.$this->outputFiles[$i]['size'].'</li>';
				echo '<li class="t4">'.$this->outputFiles[$i]['date'].'</li>';
				echo '</ul>';
				echo "\n";
			}
			echo "<br class=\"clearfloat\" />\n";
		} else {
			echo "<p id='sorry'>��Ǹ����ʱû���ļ��ṩ����</p>\n";
		}
		echo "</div>\n";
	}

	// �������Ա��¼�ɹ�ҳ��
	function showLoginedAdmin() {
		echo "<div id='logined'>\n";
		echo "<div>��ù���Ա����ӭ��¼�����������ܽ��еĲ�����</div>\n";
		echo "<div>1��ɾ���ļ�����ס��Ctrl�������Ҫɾ�����ļ�</div>\n";
		echo "<div>2���ϴ��ļ���������ϴ���СΪ".ini_get('upload_max_filesize')."���ļ��� �������ͨ��FTP���ϴ�������ļ���</div>\n";
		echo "<div>3��<a href='?loginout'>ע���˳�</a></div>\n";
		if ($this->upAnyFile) {
			echo "<div>�������ã�������ϴ��κ����͵��ļ������ϴ��󲻷������غ�׺���ļ����޷���ʾ�ġ�</div>\n";
		} else {
			$upLoad = count($this->tarFiles) == 0 ? '��������' : implode('��', $this->tarFiles);
			echo "<div>�������ã�������ϴ����ļ������У�".$upLoad."</div>\n";
		}
		echo "</div>\n";
	}

	// ����ϴ���
	function showUploadForm() {
		echo "<div id='upload'>\n";
		echo "<form action=\"?upload\" method=\"post\" enctype=\"multipart/form-data\">\n";
		echo "<input type='file' name='file' class='file' />\n";
		echo "<input type='hidden' name='dirid' value='".$this->dirId."' />\n";
		echo "<input type='submit' value='�ϴ�' />\n";
		echo "</form>\n";
		echo "</div>\n";
	}

	// ���Ŀ¼������
	function showTopbar() { // debug
		echo "<div id='topbar'>\n";
		if ($_SESSION['admin']) {
			$this->showUploadForm();
		}
		$dir = $this->tarDirsName[$this->dirId] ? $this->tarDirsName[$this->dirId] : $this->tarDirs[$this->dirId];
		echo "��ǰλ��:<span>".$dir."</span>\n";
		echo "<select onchange=\"javascript:window.location=this.value;\">\n";
		echo "<option>�ļ�����</option>\n";
		echo "<option value='?type=date&amp;order=asc&amp;dir=".$this->dirId."'>���� ����</option>\n";
		echo "<option value='?type=date&amp;order=desc&amp;dir=".$this->dirId."'>���� ����</option>\n";
		echo "<option value='?type=size&amp;order=asc&amp;dir=".$this->dirId."'>��С ����</option>\n";
		echo "<option value='?type=size&amp;order=desc&amp;dir=".$this->dirId."'>��С ����</option>\n";
		echo "<option value='?type=name&amp;order=asc&amp;dir=".$this->dirId."'>���� ����</option>\n";
		echo "<option value='?type=name&amp;order=desc&amp;dir=".$this->dirId."'>���� ����</option>\n";
		echo "<option value='?type=suffix&amp;order=asc&amp;dir=".$this->dirId."'>���� ����</option>\n";
		echo "<option value='?type=suffix&amp;order=desc&amp;dir=".$this->dirId."'>���� ����</option>\n";
		echo "</select>\n";
		echo "</div>\n";
	}

	// ���ҳ�浼����
	function showBottombar() {
		echo "<div id='bottombar'>\n";
		echo "[ �ļ���Ŀ:<span>".$this->fileNum."</span>�� ÿҳ��ʾ:<span>".$this->numPerPage."</span>�� ]\n";
		echo "[ ��ǰ��<span>".$this->pages['now']."</span>ҳ ��<span>".$this->pages['max']."</span>ҳ ]\n";
		$this->Paging($_SERVER['argv'][0]);
		echo "</div>\n";
	}

	// �����ҳ
	function Paging($ReQuest) {
		if ($this->pages['max'] == 0) {
			$this->pages['max'] = 1;
		}
		$ReQuest = isset($ReQuest) ? preg_replace("/page=\d{0,}&{0,1}/",'', $ReQuest) : '';
		if ($ReQuest != '') $ReQuest = '&amp;'.$ReQuest;
		echo $this->pages['pre'] == '' ? " [��һҳ] " : "<a href=\"?page=".$this->pages['pre'].$ReQuest."\" title=\" ��һҳ \"> [��һҳ] </a>\n";
		echo "<select onchange=\"javascript:window.location='?page='+this.value\">\n";
		for ($i = 1; $i <= $this->pages['max']; $i ++) {
			echo "<option value='".$i.$ReQuest."'";
			if ($this->pages['now'] == $i) {
				echo " selected='selected'";
			}
			echo ">��".$i."ҳ</option>\n";
		}
		echo "</select>\n";
		echo $this->pages['next'] == '' ? " [��һҳ] \n" : "<a href=\"?page=".$this->pages['next'].$ReQuest."\" title=\" ��һҳ \"> [��һҳ] </a>\n";
	}
	
	// �����Ҫ������ʵ�Ŀ¼������
	function showLoginTopbar() {
		echo "<div id='topbar'>\n";
		$dir = $this->tarDirsName[$this->dirId] ? $this->tarDirsName[$this->dirId] : $this->tarDirs[$this->dirId];
		echo "��ǰλ��:<span>".$dir."</span><span id='tip'>( Password? )</span>\n";
		echo "</div>\n";
	}

	// �����¼��
	function showLoginDir() {
		echo "<div id='login'>\n";
		echo "<form action='?logindir' method='POST'>\n";
		echo "Password:\n";
		echo "<input type='password' name='password' class='pwd' />\n";
		echo "<input type='submit' value='submit' class='sbmit'/>\n";
		echo "<input type='hidden' name='logindir' value='".$this->dirId."' />\n";
		echo "</form>\n";
		echo "</div>\n";
	}

	// ���head����HTML����
	function showHeader() {
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
		echo "<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\" />\n";
		echo "<title>".$this->title."</title>\n";
		$this->showCSS();
		$this->showJS();
		echo "</head>\n";
	}

	// ���JAVASCRIPT����
	function showJS() {
		if ($_SESSION['admin']) {
			echo "<script type='text/javascript'>\n";
			echo "function deletef(filename, filepath) {\n";
			echo "question=confirm('ȷʵҪɾ���ļ� ��'+filename+'�� ��');\n";
			echo "if(question==true) {\n";
			echo "window.open('?delete='+filepath);\n";
			echo "}\n";
			echo "else {\n";
			echo "return false;\n";
			echo "}\n";
			echo "return false;\n";
			echo "}\n";
			echo "</script>\n";
		}
	}

	// ���body����HTML����
	function showBody() {
		echo "<body>\n";
		echo "<div id='box'>\n";
		//echo "<h2 id='title'>".$this->title."</h2>\n";
		$this->showDirs();
		echo "<div id='files'>\n";
		if ($this->needsPwd && $_SESSION['Dir'.$this->dirId] != $this->tarDirsPwd[$this->dirId] && $_SESSION['admin'] != true) {
			$this->showLoginTopbar();
			$this->showLoginDir();
		} elseif (isset($_GET['adminlogin'])) {
			if ($_SESSION['admin']) {
				$this->showLoginedAdmin();
			} else {
				$this->tarDirsName[$this->dirId] = '�����¼';
				$this->showLoginTopbar();
				$this->dirId = -1;
				$this->showLoginDir();
			}
		} else {
			$this->readData();
			$this->getFileInfo();
			if ($this->fileNum > 0) {
				$this->sortArray($_GET['order'], $_GET['type']);
				$this->getPageInfo($_GET['page']);
				$this->fmtFiles();
			}
			$this->showTopbar();
			$this->showFiles();
			$this->showBottombar();
		}
		echo "</div>\n";
		echo "</div>\n";

		echo "</body>\n";
		echo "</html>\n";
	}

	// ���HTML����
	function showHTML() {
		$this->showHeader();
		$this->checkDir();
		$this->showBody();
	}

	function DataDownload($passWord, $title, $numPerPage, $tarFiles	, $voidFiles, $tarDirs, $tarDirsName, $tarDirsPwd, $cutSuffix, $upAnyFile) {
		$this->passWord			=& $passWord;
		$this->title					=& $title;
		$this->numPerPage		=& $numPerPage;
		$this->tarFiles			=& $tarFiles;
		$this->voidFiles			=& $voidFiles;
		$this->tarDirs				=& $tarDirs;
		$this->tarDirsName		=& $tarDirsName;
		$this->tarDirsPwd		=& $tarDirsPwd;
		$this->cutSuffix			=& $cutSuffix;
		$this->upAnyFile			=& $upAnyFile;
	}
}
/*------------------------------ DataDownload����� ------------------------------*/
error_reporting(0);
header("content-Type: text/html; charset=GB2312");
$dD = new DataDownload($PW, $TT, $NPP, $TF	, $VF, $TD, $TDN, $TDP, $CS, $UAF);
if ($_GET['filename'])
{
	$dD->downloadFile($_GET['filename'], $_GET['dir']);
}
else
{
session_start();
if ($_GET['delete'])
{
	$dD->deleteFile($_GET['delete'], $_GET['dir']);
} 
elseif (isset($_GET['upload']))
{
	$dD->uploadFile();
}
elseif (isset($_GET['logindir']))
{
	$dD->loginDir();
}
elseif (isset($_GET['loginout']))
{
	$dD->adminLoginout();
}
else
{
	$dD->showHTML();
}
}
?>