<?php

use Adianti\Registry\TSession;

class EmpresaForm extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();

        $this->setDatabase('cnpj_full'); // define the database
        $this->setActiveRecord('Empresa'); // define the Active Record
        $this->addFilterField('cnpj', 'like', 'cnpj'); //campo, operador, campo do form
        $this->addFilterField('razao_social', 'like', 'razao_social'); //campo, operador, campo do form
        $this->addFilterField('nome_fantasia', 'like', 'nome_fantasia'); //campo, operador, campo do form
        $this->addFilterField('motivo_situacao', '=', 'motivo_situacao'); //campo, operador, campo do form
        $this->setDefaultOrder('cnpj', 'asc'); // define the default order

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Empresas');
        $this->form->generateAria(); // automatic aria-label

        $situacaoCadastralControler = new SituacaoCadastralEmpresa();
        $listSituacaoCadastral = $situacaoCadastralControler->getList();
        
        $cnpjLabel = 'CNPJ';
        $formDinCnpjField = new TFormDinCnpjField('cnpj',$cnpjLabel);
        $cnpj = $formDinCnpjField->getAdiantiObj();

        $comboSituacao  = new TCombo('motivo_situacao');
        $comboSituacao->addItems($listSituacaoCadastral);

        $razao_social = new TEntry('razao_social');
        $nome_fantasia = new TEntry('nome_fantasia');

        $this->form->addFields( [new TLabel($cnpjLabel)],[$cnpj],[new TLabel('Motivo Situação')], [$comboSituacao]);
        $this->form->addFields( [new TLabel('Razão Social')],[$razao_social]);
        $this->form->addFields( [new TLabel('Nome Fantasia')], [$nome_fantasia] );


        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
        $this->form->addAction('Find', new TAction([$this, 'onSearch']), 'fa:search blue');        
        $this->form->addActionLink('Clear',  new TAction([$this, 'clear']), 'fa:eraser red');

        // keep the form filled with the search data
        $this->form->setData( TSession::getValue('StandardDataGridView_filter_data') );

        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        $this->datagrid->datatable = 'true'; // turn on Datatables
        
        // add the columns
        $this->datagrid->addColumn( new TDataGridColumn('cnpj','CNPJ','left') );
        $this->datagrid->addColumn( new TDataGridColumn('matriz_filial','Matriz/Filial','left') );
        $this->datagrid->addColumn( new TDataGridColumn('razao_social','Razão Social','left') );
        $this->datagrid->addColumn( new TDataGridColumn('nome_fantasia','Nome Fantasia','left') );
        $this->datagrid->addColumn( new TDataGridColumn('situacao','Situação','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_situacao','data_situacao','left') );
        $this->datagrid->addColumn( new TDataGridColumn('motivo_situacao','Motivo Situacao','left') );
        $this->datagrid->addColumn( new TDataGridColumn('nm_cidade_exterior','nm_cidade_exterior','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_pais','cod_pais','left') );
        $this->datagrid->addColumn( new TDataGridColumn('nome_pais','nome_pais','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_nat_juridica','cod_nat_juridica','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_inicio_ativ','data_inicio_ativ','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cnae_fiscal','cnae_fiscal','left') );
        $this->datagrid->addColumn( new TDataGridColumn('tipo_logradouro','tipo_logradouro','left') );
        $this->datagrid->addColumn( new TDataGridColumn('logradouro','logradouro','left') );
        $this->datagrid->addColumn( new TDataGridColumn('numero','numero','left') );
        $this->datagrid->addColumn( new TDataGridColumn('complemento','complemento','left') );
        $this->datagrid->addColumn( new TDataGridColumn('bairro','bairro','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cep','cep','left') );
        $this->datagrid->addColumn( new TDataGridColumn('uf','uf','left') );
        $this->datagrid->addColumn( new TDataGridColumn('cod_municipio','cod_municipio','left') );
        $this->datagrid->addColumn( new TDataGridColumn('municipio','Municipio','left') );
        $this->datagrid->addColumn( new TDataGridColumn('ddd_1','ddd_1','left') );
        $this->datagrid->addColumn( new TDataGridColumn('telefone_1','telefone_1','left') );
        $this->datagrid->addColumn( new TDataGridColumn('ddd_2','ddd_2','left') );
        $this->datagrid->addColumn( new TDataGridColumn('telefone_2','telefone_2','left') );
        $this->datagrid->addColumn( new TDataGridColumn('ddd_fax','ddd_fax','left') );
        $this->datagrid->addColumn( new TDataGridColumn('num_fax','num_fax','left') );
        $this->datagrid->addColumn( new TDataGridColumn('email','email','left') );
        $this->datagrid->addColumn( new TDataGridColumn('qualif_resp','qualif_resp','left') );
        $this->datagrid->addColumn( new TDataGridColumn('capital_social','capital_social','left') );
        $this->datagrid->addColumn( new TDataGridColumn('porte','porte','left') );
        $this->datagrid->addColumn( new TDataGridColumn('opc_simples','opc_simples','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_opc_simples','data_opc_simples','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_exc_simples','data_exc_simples','left') );
        $this->datagrid->addColumn( new TDataGridColumn('opc_mei','opc_mei','left') );
        $this->datagrid->addColumn( new TDataGridColumn('sit_especial','sit_especial','left') );
        $this->datagrid->addColumn( new TDataGridColumn('data_sit_especial','data_sit_especial','left') );

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));

        // creates the page structure using a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        // add the table inside the page
        parent::add($vbox);
    }

    /**
     * Clear filters
     */
    function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}
