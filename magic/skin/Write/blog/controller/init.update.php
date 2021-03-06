<?php if(!defined("__MAGIC__")) exit; 
// 비회원일 때 캡챠 검사
if(!$this->Config('mb','login')) Captcha::Inst()->Check();

$key = GV::Number($this->KN());
$tbn = $this->TBN();
$board = Board::Inst()->bo_no($this->bo_no);
$bo_no = $board->bo_no;

$clear = $this->Clear();
$clear['wr_content'] = Editor::Inst('',$board->bo_editor)->db_in($_POST['wr_content']);

$clear['wr_state'] = 0;
if($_POST['opt_notice']) {
	$clear['wr_state'] = $clear['wr_state']|$this->Config('state', 'notice');
}
if($_POST['opt_secret']) {
	$clear['wr_state'] = $clear['wr_state']|$this->Config('state', 'secret');
}

// 비회원일 경우에 비밀번호 검사를 함
// 기존의 비밀번호를 변경할때 사용함
if(!$this->Config('mb','login')) {
	$clear['mb_no'] = 0;
	if($clear['wr_password']!=$_POST['wr_password_check'])
		Dialog::alert('[비밀번호/비밀번호확인]이 일치하지 않습니다.');
	if(!$clear['wr_password'])
		Dialog::alert('비밀번호를 입력해 주세요.');
	$clear['wr_password'] = $this->Sql('password', $clear['wr_password']);
} else {
	$clear['wr_writer'] = Member::Inst()->mb_nick;
}

// 최근게시글을 위해 게시글이 출력되는 아이디를 저장함
$r = GV::String('r');
$id1 = GV::String('id1');
$id2 = GV::String('id2');
$qstr = array();
if($r) $qstr[] = 'r='.$r;
if($id1) $qstr[] = 'id1='.$id1;
if($id2) $qstr[] = 'id2='.$id2;
$clear['last_id'] = '?'.implode('&', $qstr);

if(!$clear['wr_subject'])	 Dialog::alert('제목을 입력해 주세요.');
if(!$clear['wr_content'])	 Dialog::alert('내용을 입력해 주세요.');
if(!$clear['wr_writer'])	 Dialog::alert('글쓴이를 입력해 주세요.');

// 업데이트 날짜
$clear['wr_update'] = 'NOW()';

// 분류
if($_POST['ca1']) $clear['wr_category'][] = $_POST['ca1']; 
if($_POST['ca2']) $clear['wr_category'][] = $_POST['ca2']; 
if(is_array($clear['wr_category'])) $clear['wr_category'] = implode('|',$clear['wr_category']); 

$clear['wr_state'] = 0;
if($_POST['opt_notice']) {
	$clear['wr_state'] = $clear['wr_state']|$this->Config('state', 'notice');
}

DB::Get()->update($tbn, $clear, ' WHERE wr_no='.$key, array('wr_update'));

// 태그 입력
$tags = $_POST['tags'];
Tag::Inst()->Action('insert', $bo_no, $key, array_unique(array_filter(explode(',', $tags))));

// 파일 업로드
File::Inst()->Action('upload', $key);
$del_files = $_POST[File::Inst()->Config('form_name', 'del')];
if(!is_array($del_files)) $del_files = array();
foreach ($del_files as $v) {
	File::Inst()->Action('delete', $v);
}

Url::GoReplace(Url::Get('',$this->Mode('name')));



