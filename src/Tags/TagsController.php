<?php

namespace Anax\Tags;

/**
 * A controller for users and admin related events.
 *
 */
class TagsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

     private $pagekey;

		/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->comments = new \Anax\Tags\Tag();
		$this->comments->setDI($this->di);
	}


    public function tagAction($id = null) {

        if(isset($id))
        {
            $this->listAction($id);
        } else {
            $this->listAllAction();
        }

    }

	public function listAllAction($orderby = null) {

		$this->user = new \Anax\Users\User();
		$this->user->setDI($this->di);

    if(isset($orderby)) {
		$all = $this->comments->query()
        ->orderby($orderby . ' DESC')
        ->execute();

        $this->theme->setTitle('Questions orderby ' . $orderby);
    } else {
        $all = $this->comments->query()
        ->orderby('created DESC')
        ->execute();
    }

        $this->views->add('tag/list-all', [
            'comments' => $all,
			'user' => $this->user,
            'orderby' => $orderby,
            'title' => "",
        ]);

		// Formulär
		//$this->addAction();


	}

    public function getLatestTagsAction()
    {
        $all = $this->comments->query()
        ->orderby('rate DESC LIMIT 5')
        ->execute();

        $this->views->add('tag/widget', [
            'latest_tags' => $all,
            'title' => "Populäraste taggarna",
        ]);
    }


    public function listAction($id) {

		$this->user = new \Anax\Users\User();
		$this->user->setDI($this->di);

		// Kommentarer
        $q = $this->comments->find($id);
		$all = $this->comments->query()
        ->where('id = ?')
        ->execute([$id]);

        $this->theme->setTitle($q->getProperties()['heading']);

        $this->views->add('tag/list-all', [
            'comments' => $all,
			'user' => $this->user,
            'title' => "Kommentarer",
        ]);
	}

    public function updateAction($id = null)
    {
        $this->theme->setTitle('Uppdatera en tag');

        if (!isset($id)) {
            die("Missing id");
        }

        $tag = $this->comments->find($id);
        $desc = $tag->getProperties()['description'];
        $name = $tag->getProperties()['name'];

        $form = new \Anax\HTMLForm\FormUpdateTag($id, $desc, $name);
        $form->setDI($this->di);
        $status = $form->check();

        $this->di->views->add('users/form', [
            'title' => "Uppdatera en tag",
            'content' => $form->getHTML(),
            ]);

        if($status === true) {
            echo 'true';
            $url = $this->url->create('list');
            $form->response->redirect($url);
        }
    }


    public function resetDatabaseAction() {
        $this->db->setVerbose();

        $this->db->dropTableIfExists('tag')->execute();

        $this->db->createTable(
            'tag',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'name'       => ['varchar(80)'],
                'description'   => ['text'],
                'rate'          => ['integer'],
                'created'       => ['datetime'],
            ]
        )->execute();
    }
}
