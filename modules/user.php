<?php
/**
 * Работа с пользователями, роля пользователей
 */
class _user {
    /**
     * Количество сткро на страницу
     * @var int
     */
    public static $per_page = 10;
    
    function __construct() {
        
    }
    /**
     * Получение списка ролей пользователей
     * @return array
     */
    public static function roles(){
        $query = _db::query("SELECT id_role, name_role FROM roles");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Количество пользователей
     * 
     * @return int
     */
    public static function count(){
        return _db::query_onevalue("SELECT COUNT(login) FROM customers");
    }
    /**
     * Список пользователей
     * 
     * @param type $start_pos Наачальная позиция
     * @param type $perpage Конечная позиция
     * @return array
     */
    public static function get_users($start_pos, $perpage){
        $query = _db::query("SELECT customer_id, name, login, email, name_role
                    FROM customers
                    LEFT JOIN roles
                        ON customers.id_role = roles.id_role
                    WHERE login IS NOT NULL LIMIT ?, ?", array($start_pos, $perpage));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Добавление пользователя
     * 
     * @return boolean
     */
    public static function add(){
        $error = ''; // флаг проверки пустых полей

        $login = trim($_POST['login']);
        $password = trim($_POST['password']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $id_role = (int)$_POST['id_role'];

        if(empty($login)) $error .= '<li>Не указан логин</li>';
        if(empty($password)) $error .= '<li>Не указан пароль</li>';
        if(empty($name)) $error .= '<li>Не указано ФИО</li>';
        if(empty($email)) $error .= '<li>Не указан Email</li>';

        if(empty($error)){
            // если все поля заполнены
            // проверяем нет ли такого юзера в БД
            if( self::checklogin($login) ){
                // если такой логин уже есть
                $_SESSION['users_add']['res'] = "<div class='error'>Пользователь с таким логином уже зарегистрирован на сайте. Введите другой логин.</div>";
                $_SESSION['users_add']['name'] = $name;
                $_SESSION['users_add']['email'] = $email;
                $_SESSION['users_add']['password'] = $password;
                return false;
            }else{
                // если все ок - регистрируем
                $pass = md5($password);
                $query = _db::query("INSERT INTO customers (name, email, login, password, id_role)
                            VALUES (?, ?, ?, ?, ?)", array($name, $email, $login, $pass, $id_role) );
                if($query->rowCount() > 0){
                    // если запись добавлена
                    $_SESSION['answer'] = "<div class='success'>Пользователь добавлен.</div>";
                    return true;
                }else{
                    $_SESSION['users_add']['res'] = "<div class='error'>Ошибка!</div>";
                    $_SESSION['users_add']['login'] = $login;
                    $_SESSION['users_add']['name'] = $name;
                    $_SESSION['users_add']['email'] = $email;
                    $_SESSION['users_add']['password'] = $password;
                    return false;
                }
            }
        }else{
            // если не заполнены обязательные поля
            $_SESSION['users_add']['res'] = "<div class='error'>Не заполнены обязательные поля: <ul> $error </ul></div>";
            $_SESSION['users_add']['login'] = $login;
            $_SESSION['users_add']['name'] = $name;
            $_SESSION['users_add']['email'] = $email;
            $_SESSION['users_add']['password'] = $password;
            return false;
        }
    }
    /**
     * Удаление пользователя
     * 
     * @param int $user_id
     */
    public static function del($user_id){
        if($_SESSION['auth']['user_id'] == $user_id){
            $_SESSION['answer'] = "<div class='error'>Вы не можете удалить сами себя!</div>";
        }else{
            $query = _db::query("DELETE FROM customers WHERE customer_id = ?", array($user_id) );
            if( $query->rowCount() > 0){
                $_SESSION['answer'] = "<div class='success'>Пользователь удален</div>";
            }else{
                $_SESSION['answer'] = "<div class='error'>Ошибка удаления!</div>";
            }
        }
    }
    /**
     * Проверка логина на наличии в базе
     * 
     * @param string $login
     * @return boolean
     */
    public static function checklogin($login){
        $res = _db::query_onevalue("SELECT count(1) FROM customers WHERE login = ?", array($login));
        return (bool)$res;
    }
    /**
     * Получение данных пользователя
     * 
     * @param int $user_id
     * @return array
     */
    public static function get($user_id){
        $query = _db::query("SELECT name, email, phone, address, login, id_role FROM customers WHERE customer_id = ?", array($user_id) );
        return $query->fetch( PDO::FETCH_ASSOC );
    }
    /**
     * Редактирование пользователя
     * 
     * @param int $user_id
     * @return boolean
     */
    public static function edit($user_id){
        $login = trim($_POST['login']);
        $password = md5( trim($_POST['password']) );
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $id_role = (int)$_POST['id_role'];        
        
        
        
        $query = _db::query("UPDATE customers SET name=?, email=?, login=?, password=?, id_role=? WHERE customer_id = ?", 
                array($name, $email, $login, $password, $id_role, $user_id) );
        if($query->rowCount() > 0){
            $_SESSION['answer'] = "<div class='success'>Данные обновлены</div>";
            if($user_id == $_SESSION['auth']['user_id']){
                $_SESSION['auth']['admin'] = htmlspecialchars($_POST['name']);
            }
            return true;
        }else{
            $_SESSION['users_edit']['res'] = "<div class='error'>Ошибка</div>";
            return false;
        }
    }   
    
    /**
     * Регистрация нового пользователя
     */
    public static function registration(){
        $error = ''; // флаг проверки пустых полей

        $login = trim($_POST['login']);
        $pass = trim($_POST['pass']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        if(empty($login)) $error .= '<li>Не указан логин</li>';
        if(empty($pass)) $error .= '<li>Не указан пароль</li>';
        if(empty($name)) $error .= '<li>Не указано ФИО</li>';
        if(empty($email)) $error .= '<li>Не указан Email</li>';
        if(empty($phone)) $error .= '<li>Не указан телефон</li>';
        //if(empty($address)) $error .= '<li>Не указан адрес</li>';

        if(empty($error)){
            // если все поля заполнены
            // проверяем нет ли такого юзера в БД
            if( self::checklogin($login) ){
                // если такой логин уже есть
                $_SESSION['reg']['res'] = "<div class='error'>Пользователь с таким логином уже зарегистрирован на сайте. Введите другой логин.</div>";
                $_SESSION['reg']['name'] = $name;
                $_SESSION['reg']['email'] = $email;
                $_SESSION['reg']['phone'] = $phone;
                $_SESSION['reg']['addres'] = $address;
            }else{
                // если все ок - регистрируем
                $pass = md5($pass);
                $query = _db::query("INSERT INTO customers (name, email, phone, address, login, password)
                            VALUES (?, ?, ?, ?, ?, ?)", array($name, $email, $phone, $address, $login, $pass) );
                if( $query->rowCount() > 0){
                    // если запись добавлена
                    $_SESSION['reg']['res'] = "<div class='success'>Регистрация прошла успешно.</div>";
                    $_SESSION['auth']['user'] = $_POST['name'];
                    $_SESSION['auth']['customer_id'] = _db::$db->lastInsertId();
                    $_SESSION['auth']['email'] = $email;
                }else{
                    $_SESSION['reg']['res'] = "<div class='error'>Ошибка!</div>";
                    $_SESSION['reg']['login'] = $login;
                    $_SESSION['reg']['name'] = $name;
                    $_SESSION['reg']['email'] = $email;
                    $_SESSION['reg']['phone'] = $phone;
                    $_SESSION['reg']['addres'] = $address;
                }
            }
        }else{
            // если не заполнены обязательные поля
            $_SESSION['reg']['res'] = "<div class='error'>Не заполнены обязательные поля: <ul> $error </ul></div>";
            $_SESSION['reg']['login'] = $login;
            $_SESSION['reg']['name'] = $name;
            $_SESSION['reg']['email'] = $email;
            $_SESSION['reg']['phone'] = $phone;
            $_SESSION['reg']['addres'] = $address;
        }
    }
    /**
     * Авторизация
     */
    public static function authorization(){
        $login = trim($_POST['login']);
        $pass = trim($_POST['pass']);

        if(empty($login) OR empty($pass)){
            // если пусты поля логин/пароль
            $_SESSION['auth']['error'] = "Поля логин/пароль должны быть заполнены!";
        }else{
            // если получены данные из полей логин/пароль
            $pass = md5($pass);

            $query = _db::query("SELECT customer_id, name, email FROM customers WHERE login = ? AND password = ? LIMIT 1", array($login, $pass));
            if($query->rowCount() == 1){
                // если авторизация успешна
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $_SESSION['auth']['customer_id'] = $row['customer_id'];
                $_SESSION['auth']['user'] = $row['name'];
                $_SESSION['auth']['email'] = $row['email'];
            }else{
                // если неверен логин/пароль
                $_SESSION['auth']['error'] = "Логин/пароль введены неверно!";
            }
        }
    }


    
}
