<?php

/**
 * Description of newsAgregator
 * CRUD работы с новостями
 * методы : добавить, отредактировать, удалить, просмотреть список (пейджинг), просмотреть полную новость.
 * Поля: id, date, title, description.
 * @author Mikhail
 */
class newsAgregator
{

    /**
     * Сколько символов оставлять от полной новости в пйджинг
     * @var int 
     */
    private $substrDescription = 100;

    /**
     * Колличество выводимых новостей в пейджинге
     * @var int 
     */
    private $sizeNewsPaging = 10;

    /**
     * наименование кэш полей
     * @var array 
     */
    private $cacheNames = array(
        "paging" => "paging",
        "news" => "news"
    );

    public function __construct()
    {
        ;
    }

    /**
     * Получить полную новость 
     * @param type $id
     * @return type
     */
    public function getFullNews($id)
    {
        if(empty($id))
            return ;
        $news = systemRegistry::get("cache")->get(
                $this->cacheNames['news'] . $id
        );
        if (!$news) {
            $stmt = systemRegistry::get("db")->prepare(
                    "SELECT id, date, title, description FROM newsAgregator WHERE id = ?"
            );
            $stmt->execute(array($id));
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            systemRegistry::get("cache")->add(
                    $this->cacheNames['news'] . $id, $news
            );
        }
        return $news;
    }

    /**
     * Получитьь список новостей для пейджинага , выбрать новость , обрезать описания , добавить в кэш
     * @param int $page
     * @return array массив обрезаных новостей + общее кол-вл новостей для пагинации
     */
    public function getPaging($page = 1)
    {
        $dataPaging = systemRegistry::get('cache')->get(
                $this->cacheNames['paging']
        );
        if (!$dataPaging) {
            $stmt = systemRegistry::get('db')->prepare(
                    "SELECT 
                        SQL_CALC_FOUND_ROWS 
                        id, title, SUBSTRING(description , 1 , " . $this->substrDescription . ") as description
                    FROM 
                        newsAgregator 
                    ORDER BY date DESC LIMIT ? ," . $this->sizeNewsPaging . " ; "
            );
            $stmt->execute(array(
                ((int) $page * $this->sizeNewsPaging)
            ));
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //получить общее колличество резкльтатов лимита , для генерации пагинации
            $dbres = systemRegistry::get('db')->query("SELECT FOUND_ROWS()");
            $rowCount = (int) $dbres->fetchColumn();
            $dataPaging = array(
                "row" => $row,
                "rowCount" => $rowCount
            );
            //добавим данные в кэш
            systemRegistry::get('cache')->add(
                    $this->cacheNames['paging'], $dataPaging
            );
        }
        return $dataPaging;
    }

    /**
     * Добавить новость  
     * @param array $dataNews массив полей 
     * @return int lastInsertId
     */
    public function addNews(array $dataNews)
    {
        $sql = "INSERT INTO newsAgregator 
                    ( tile , description , date ) 
                 VALUE
                    ( ? , ? , NOW() ) ;";
        if ($this->workerNews($dataNews, $sql)) {
            return systemRegistry::get('db')->lastInsertId();
        }
    }

    /**
     * Произвести редактирование поля 
     * @param int $id id новости
     * @param array $dataNews массив полей
     * @return boolean
     */
    public function editNews($id, array $dataNews)
    {
        $sql = "UPDATE link SET 
                    title = ? , description = ?
                 WHERE
                    id = " . (int) $id . " ; ";

        if ($this->workerNews($dataNews, $sql)) {
            systemRegistry::get('cache')->delete($this->cacheNames['news'] . $ids);
            systemRegistry::get('cache')->delete($this->cacheNames['paging']);
            return true;
        }
        return false;
    }

    /**
     * обработчик запросов add|edit
     * @param array $dataNews
     * @return boolean
     */
    private function workerNews(array $dataNews, $sql)
    {
        if (empty($dataNews['title']) || empty($dataNews['description']) || empty($sql)) {
            return FALSE;
        }
        //Удаляем двойные пробелы , тримим слева справа
        foreach ($dataNews as $k => $v) {
            $replace = preg_replace("#\s{2,}#i", " ", $v);
            $dataNews[$k] = trim($replace);
        }
        $stmt = systemRegistry::get('db')->prepare($sql);
        $stmt->execute(array(
            $dataNews['title'], $dataNews['description']
        ));
        return TRUE;
    }

    /**
     * Удалить 1 или несколько новостей , удалить кэш
     * @param int|array $ids
     */
    private function deleteNews($ids)
    {
        $cacheDelete = array();
        $stmt = systemRegistry::get('db')->prepare(
                "DELETE FROM newsAgregator WHERE id = ? ;"
        );
        //если пришел массив , завернем удаления в транзакцию 
        if (is_array($ids)) {
            systemRegistry::get('db')->beginTransaction();
            array_walk($ids, function($id) use ($stmt, &$cacheDelete) {
                $stmt->execute(array($id));
                $cacheDelete[] = "news" . $id;
            });
            systemRegistry::get('db')->commit();
            //удалить все кешированые 'полные новости'
            systemRegistry::get('cache')->deleteMulti($cacheDelete);
        } else {
            //Удаление 1 новости
            $stmt->execute(array($ids));
            systemRegistry::get('cache')->delete("news" . $ids);
        }
        systemRegistry::get('cache')->delete("paging");
    }

}
