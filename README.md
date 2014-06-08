newsAgregator
=============

CRUD для работы с новостями 

прмеры работы с агрегатором новостей : 
     
1 . добавить новость :

    $id = systemRegistry::set("newsAgregator")->addNews(array(
        "title" => "Супер новость!",
        "description" => "Новость замечательна!"
    ));
    
2 . редактировать новость 

    systemRegistry::set("newsAgregator")->editNews($id, array(
        "title" => "А новость то      изменилась",
        "description" => "И стала еще новее!"
    ));
    
3 . удалить новость 

    systemRegistry::set("newsAgregator")->deleteNews(id);

4 . удалить несколько новостей 

    systemRegistry::set("newsAgregator")->deleteNews(array
        (1, 2, 3, 4, 5, 6, 7)
    );

5 . получить полную версию новости 

    systemRegistry::set("newsAgregator")->getFullNews($id);

6 . получить пейджинг 2 страницу новостей 

    systemRegistry::set("newsAgregator")->getPaging(2);
    
