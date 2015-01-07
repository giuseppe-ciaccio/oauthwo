<?php

require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Mapper' . DIRECTORY_SEPARATOR . 'rset_mapper.php');
require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Mapper' . DIRECTORY_SEPARATOR . 'rsets_scopes_M.php');
require_once(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'resourceset' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'FormSearch.php');

	class Resourceset_RegistrationController extends Zend_Controller_Action
	{

		public function init()
		{
		}

		// display static views
		public function creatersAction()
		{
			$this->_helper->viewRenderer->setNoRender();

			$postdata = get_object_vars(json_decode(file_get_contents("php://input")));

			$d = " metodo create di AS ";
			var_dump($d);
			var_dump($postdata);


			$name = $postdata['name'];
			$scopes = $postdata['defscope'];

			$description = $postdata['description'];
			$setid = $postdata['id'];
			$type = $postdata['type'];

			$endpoint = $postdata['endpoint'];

			$rset_info = new Resourceset_Mapper_RSetTable();
			$rset_nuovo = new Resourceset_Model_RSet($setid, $name, $description, $type, $endpoint, $scopes);
			$rset_info->save($rset_nuovo);

			$exp_scopes = explode(",",$scopes);

			var_dump($exp_scopes);

			foreach($exp_scopes as $sc){
				$srs = new Resourceset_Mapper_RSetScopes();
				$rset_scopes_nuovo = new Resourceset_Model_ResetScopes($sc, $setid);
				$srs->save($rset_scopes_nuovo);
			}
		}


		public function searchAction(){
			$this->_helper->viewRenderer->setNoRender();
							$form = new Resourceset_Form_Search();
							$this->view->form = $form;
				$this->render();

			}

		public function searchresultAction(){

					$this->_helper->viewRenderer->setNoRender();
					$request = $this->getRequest();
					if ($request->isPost()) {
							$values = $request->getPost();
						}

					$res = new Resourceset_Mapper_RSetTable();
					$results = $res->search($values);

					$this->view->resu = $results;
					$this->render();

		}

}