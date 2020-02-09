<?php

class SocioViewForm extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->adianti_target_container = 'adianti_right_panel';
    }

    public function onView($param)
    {
        try{
            var_dump($param);

            $cnpj_cpf_socio = $param['cnpj_cpf_socio'];
            $nome_socio = $param['nome_socio'];
            $socioController = new SocioController();
            $listSocio = $socioController->selectBySocio($cnpj_cpf_socio,$nome_socio);
            $this->showFormSocioNaEmpresa($listSocio[0]);
            $this->showGridEmpresa($listSocio);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function showFormSocioNaEmpresa(Socio $socio){
        $this->form = new BootstrapFormBuilder(__CLASS__);
        $this->form->setFormTitle('Dados do Sócio na empresa '.StringHelper::formatCnpjCpf($socio->cnpj));
        $this->form->generateAria(); // automatic aria-label

        $tipoSocioControler = new TipoSocio();
        $listipoSocio = $tipoSocioControler->getList();

        $cnpjLabel = 'CNPJ';
        $formDinCnpjField = new TFormDinCnpjField('cnpj',$cnpjLabel);
        $cnpj = $formDinCnpjField->getAdiantiObj();
        
        $tipo_socio = new TCombo('tipo_socio');
        $tipo_socio->addItems($listipoSocio);
        $nome_socio = new TEntry('nome_socio');

        $this->form->addFields( [new TLabel('CNPJ')],[$cnpj],[new TLabel('Tipo Sócio')],[$tipo_socio]);
        $this->form->addFields( [new TLabel('Nome')],[$nome_socio]);
        parent::add($this->form);
    }
    
    public function showGridEmpresa($listSocio){
        $empresaController = new EmpresaController();
        $listEmpresa = $empresaController->selectBySocio($listSocio);
        $panel = $this->getGridEmpresa($listEmpresa);
        parent::add($panel);
    }

    public function getGridEmpresa(array $listEmpresa){
        // create the datagrid
        $grid = new BootstrapDatagridWrapper(new TDataGrid);
        $grid->width = '100%';
        $cnpj = new TDataGridColumn('cnpj','CNPJ','left');
        $cnpj->setTransformer(function ($value) {
            return StringHelper::formatCnpjCpf($value);
        });
        $grid->addColumn( $cnpj );
        $grid->addColumn( new TDataGridColumn('razao_social','Razão Social','left') );
        $grid->addColumn( new TDataGridColumn('nome_fantasia','Nome Fantasia','left') );

        $action1 = new TDataGridAction(['EmpresaViewForm', 'onView'],  ['key' => '{cnpj}'], ['register_state' => 'false']  );
        $grid->addAction($action1, 'Detalhar Empresa', 'fa:building #7C93CF');

        $grid->createModel();
        $grid->addItems($listEmpresa);
        $panel = TPanelGroup::pack('Lista de Empresas que é socio', $grid);
        
        return $panel;
    }
}