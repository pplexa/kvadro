<?php
/**
 * Работа со страницами
 */
class _page {
    function __construct() {
        
    }
    /**
     * Отдельная страница по номеру
     * 
     * @param int $id
     * @return array
     */
    public static function get($id){
        $query = _db::query("SELECT * FROM pages WHERE page_id = ?", array($id));
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Отдельная страница по имени
     * 
     * @param string $id
     * @return array
     */
    public static function getByName($name){
        $query = _db::query("SELECT * FROM pages WHERE url = ?", array($name));
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Все страницы
     * 
     * @return type
     */
    public static function all(){
        $query = _db::query("SELECT page_id, title, position, url FROM pages ORDER BY position");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Добавление страницы
     * 
     * @return boolean
     */
    public static function add(){
        $title = trim($_POST['title']);
        $keywords = trim($_POST['keywords']);
        $description = trim($_POST['description']);
        $position = (int)$_POST['position'];
        $url = trim($_POST['url']);
        $text = trim($_POST['text']);

        if(empty($title)){
            // если нет названия
            $_SESSION['pages_add']['res'] = "<div class='error'>Должно быть название страницы!</div>";
            $_SESSION['pages_add']['keywords'] = $keywords;
            $_SESSION['pages_add']['description'] = $description;
            $_SESSION['pages_add']['position'] = $position;
            $_SESSION['pages_add']['url'] = $url;
            $_SESSION['pages_add']['text'] = $text;
            return false;
        }else{
            $query = _db::query("INSERT INTO pages (title, keywords, description, position, url, text)
                        VALUES (?, ?, ?, ?, ?, ?)", array($title, $keywords, $description, $position, $url, $text) );

            if( $query->rowCount() > 0){
                $_SESSION['answer'] = "<div class='success'>Страница добавлена!</div>";
                return true;
            }else{
                $_SESSION['pages_add']['res'] = "<div class='error'>Ошибка при добавлении страницы!</div>";
                return false;
            }

        }
    }    
    /**
     * Редактирование страницы
     * 
     * @param type $page_id
     * @return boolean
     */
    public static function edit($page_id){
        $title = trim($_POST['title']);
        $keywords = trim($_POST['keywords']);
        $description = trim($_POST['description']);
        $position = (int)$_POST['position'];
        $url = trim($_POST['url']);
        $text = trim($_POST['text']);

        if(empty($title)){
            // если нет названия
            $_SESSION['pages_edit']['res'] = "<div class='error'>Должно быть название страницы!</div>";
            return false;
        }else{
            $query = _db::query("UPDATE pages SET
                        title = ?,
                        keywords = ?,
                        description = ?,
                        position = ?,
                        url = ?,
                        text = ?
                            WHERE page_id = ?", array($title, $keywords, $description, $position, $url, $text, $page_id) );
            if( $query->rowCount() > 0){
                $_SESSION['answer'] = "<div class='success'>Страница обновлена!</div>";
                return true;
            }else{
                $_SESSION['pages_edit']['res'] = "<div class='error'>Ошибка или Вы ничего не меняли!</div>";
                return false;
            }
        }
    }
    /**
     * Удаление страницы
     * 
     * @param type $page_id
     * @return boolean
     */
    public static function del($page_id){
        $query = _db::query("DELETE FROM pages WHERE page_id = ?", array($page_id));

        if( $query->rowCount() > 0){
            $_SESSION['answer'] = "<div class='success'>Страница удалена.</div>";
            return true;
        }else{
            $_SESSION['answer'] = "<div class='error'>Ошибка удаления страницы!</div>";
            return false;
        }
    }
}
