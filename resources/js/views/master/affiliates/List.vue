<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="query.keyword" :placeholder="$t('table.keyword')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-plus" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
      <el-button v-waves :loading="downloading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">
        {{ $t('table.export') }}
      </el-button>
    </div>

    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Habilitado">
        <template slot-scope="scope">
          <el-switch v-model="scope.row.status" :active-value="'S'" :inactive-value="'N'" active-color="#13ce66" inactive-color="#ff4949" @click.native="handleChangeStatus($event, scope.row.id, scope.row.status)" />
        </template>
      </el-table-column>

      <el-table-column align="center" label="Tipo">
        <template slot-scope="scope">
          <span>{{ scope.row.tpaffiliate_name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Nome">
        <template slot-scope="scope">
          <span>{{ scope.row.fantasy_name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Tipo">
        <template slot-scope="scope">
          <span>{{ scope.row.type_person }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="CPF/CNPJ" width="150">
        <template slot-scope="scope">
          <span v-if="scope.row.type_person === 'PF'">{{ scope.row.cpf_or_cnpj | VMask('###.###.###-##') }}</span>
          <span v-else>{{ scope.row.cpf_or_cnpj | VMask('##.###.###/####-##') }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Fone">
        <template slot-scope="scope">
          <span>{{ scope.row.fone1 | VMask('(##) #####-####') }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="220">
        <template slot-scope="scope">
          <el-button type="primary" size="small" icon="el-icon-edit" @click="handleEdit(scope.row.id);">
            Editar
          </el-button>
          <el-button type="danger" size="small" icon="el-icon-delete" @click="handleDelete(scope.row.id, scope.row.name);">
            Excluir
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />

    <el-dialog :title="'Cadastrar novo affiliado'" :visible.sync="dialogFormCreateVisible" width="80%">
      <div v-loading="AffiliateCreating" class="form-container">
        <el-form ref="affiliateFormCreate" :rules="rules" :model="newAffiliate" label-position="left" style="max-width: 900px;">
          <el-row :gutter="40" class="panel-group">
            <el-col :xs="{span: 24}" :sm="{span: 16}" :md="{span: 12}" :lg="{span: 12}" :xl="{span: 12}">
              <el-form-item :label="$t('Tipo do Afiliado')" prop="tpaffiliate">
                <el-select v-model="newAffiliate.tpaffiliate" class="filter-item" placeholder="Selecione um tipo de afiliado" clearable filterable style="width: 242px;">
                  <el-option v-for="item in listTpAffiliate" :key="item.id" :label="item.name | uppercaseFirst" :value="item.id" />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :xs="{span: 24}" :sm="{span: 16}" :md="{span: 12}" :lg="{span: 12}" :xl="{span: 12}">
              <el-form-item :label="$t('Tipo da pessoa')" label-width="150" prop="type_person">
                <el-select v-model="newAffiliate.type_person" class="filter-item" style="width: 242px;">
                  <el-option v-for="item in typePerson" :key="item.value" :label="item.label | uppercaseFirst" :value="item.value" />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="40" class="panel-group">
            <el-col :xs="{span: 24}" :sm="{span: 16}" :md="{span: 12}" :lg="{span: 12}" :xl="{span: 12}">
              <el-form-item :label="$t('Razão social')" prop="corporate_name">
                <el-input v-model="newAffiliate.corporate_name" />
              </el-form-item>
            </el-col>
            <el-col :xs="{span: 24}" :sm="{span: 16}" :md="{span: 12}" :lg="{span: 12}" :xl="{span: 12}">
              <el-form-item :label="$t('Nome fantasia')" prop="fantasy_name">
                <el-input v-model="newAffiliate.fantasy_name" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="40" class="panel-group">
            <el-col :xs="{span: 24}" :sm="{span: 7}" :md="{span: 6}" :lg="{span: 6}" :xl="{span: 6}">
              <el-form-item :label="$t('Cep')" prop="zip_code">
                <el-input v-model="newAffiliate.zip_code" v-mask="'#####-###'" @change="handleSearchZipCodeBrazil(newAffiliate.zip_code)" />
              </el-form-item>
            </el-col>
            <el-col :xs="{span: 24}" :sm="{span: 10}" :md="{span: 12}" :lg="{span: 12}" :xl="{span: 12}">
              <el-form-item :label="$t('Rua')" prop="street">
                <el-input v-model="newAffiliate.street" />
              </el-form-item>
            </el-col>
            <el-col :xs="{span: 24}" :sm="{span: 7}" :md="{span: 6}" :lg="{span: 6}" :xl="{span: 6}">
              <el-form-item :label="$t('Número')" prop="number">
                <el-input v-model="newAffiliate.number" v-mask="'#########'" />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormCreateVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createAffiliate()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog :title="'Editar affiliado id ('+ updateAffiliate.id +')'" :visible.sync="dialogFormEditVisible" @close="getList">
      <div v-loading="affiliateEditing" class="form-container">
        <el-form ref="affiliateFormEdit" :rules="rules" :model="updateAffiliate" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="$t('name')" prop="name">
            <el-input v-model="updateAffiliate.name" />
          </el-form-item>
          <el-form-item :label="$t('description')" prop="description">
            <el-input v-model="updateAffiliate.description" type="textarea" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="editAffiliate(updateAffiliate.id)">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>

import OthersApiRequest from '@/api/external/others-api'; // class api OthersApiRequest
import TpaffiliateResource from '@/api/master/tpaffiliate'; // class api tpaffiliate
import AffiliateResource from '@/api/master/affiliate'; // class api affiliate
import { validateZipCode } from '@/utils/validate';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import waves from '@/directive/waves'; // Waves directive

const othersApiRequest = new OthersApiRequest();
const tpaffiliateResource = new TpaffiliateResource();
const affiliateResource = new AffiliateResource();

export default {
  name: 'AffiliateList',
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      list: null,
      listTpAffiliate: {},
      typePerson: [
        {
          value: 'PF',
          label: 'Pessoa Física',
        },
        {
          value: 'PJ',
          label: 'Pessoa Jurídica',
        },
      ],
      total: 0,
      loading: true,
      downloading: false,
      AffiliateCreating: false,
      affiliateEditing: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        tpaffiliate: '',
        typeperson: '',
        status: '',
      },
      queryStep: {
        status: 'S',
      },
      newAffiliate: {},
      updateAffiliate: {},
      dialogFormCreateVisible: false,
      dialogFormEditVisible: false,
      //
      currentAffiliate: {
        id: 0,
        status: null,
        name: '',
        description: '',
      },
      rules: {
        tpaffiliate: [{ required: true, message: 'Selecione um tipo de afiliado', trigger: ['blur', 'change'] }],
        type_person: [{ required: true, message: 'Selecione um tipo de pessoa', trigger: ['blur', 'change'] }],
        corporate_name: [
          { required: true, message: 'Razão social está vazia', trigger: 'blur' },
          { minlength: 5, message: 'deve conter no mínimo 4 caracteres', trigger: ['blur', 'change'] },
        ],
        fantasy_name: [
          { required: true, message: 'Nome fantasia está vazio', trigger: 'blur' },
          { minlength: 5, message: 'deve conter no mínimo 4 caracteres', trigger: ['blur', 'change'] },
        ],
        zip_code: [{ required: true, message: 'Cep está vazio', trigger: ['blur', 'change'] }],
        street: [{ required: true, message: 'Rua está vazia', trigger: ['blur', 'change'] }],
        number: [{ required: true, message: 'Número está vazio', trigger: ['blur', 'change'] }],
      },
    };
  },
  computed: {
  },
  created() {
    this.resetNewAffiliate();
    this.getList();
  },
  mounted() {
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await affiliateResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },
    async getListOthers() {
      const response = await tpaffiliateResource.listWithParams(this.queryStep);
      this.listTpAffiliate = response.data;
    },
    async searchZipCodeBrazil(zipcode) {
      try {
        const response = await othersApiRequest.searchZipCodeBrazil(zipcode);
        if (response.erro){
          this.newAffiliate.street = '';
          this.$message({
            message: 'CEP não existe',
            type: 'error',
            duration: 5 * 1000,
            showClose: true,
          });
        } else {
          this.newAffiliate.street = response.logradouro;
        }
      } catch (error) {
        if (error.response) {
          this.$message({
            message: 'Erro ao consultar o CEP',
            type: 'error',
            duration: 5 * 1000,
            showClose: true,
          });
        } else if (error.request) {
          this.$message({
            message: 'Erro ao consultar o CEP',
            type: 'error',
            duration: 5 * 1000,
            showClose: true,
          });
        } else {
          this.$message({
            message: 'Erro ao consultar o CEP',
            type: 'error',
            duration: 5 * 1000,
            showClose: true,
          });
        }
      }
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleCreate() {
      this.getListOthers();
      this.resetNewAffiliate();
      this.dialogFormCreateVisible = true;
      this.$nextTick(() => {
        this.$refs['affiliateFormCreate'].clearValidate();
      });
    },
    handleEdit(id) {
      this.updateAffiliate = this.list.filter(tpAffiliateFiltered => tpAffiliateFiltered.id === id);
      // this.updateAffiliate = Object.values(this.list).filter(tpAffiliateFiltered => tpAffiliateFiltered.id === id);
      this.updateAffiliate = this.updateAffiliate[0];
      this.dialogFormEditVisible = true;
      this.$nextTick(() => {
        this.$refs['affiliateFormEdit'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('Confirma excluir o tipo de afiliado ' + name + '. Confirmar?', 'Warning', {
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }).then(() => {
        affiliateResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: 'Excluído com sucesso',
          });
          this.handleFilter();
        }).catch(error => {
          console.log(error);
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Exclusão cancelada',
        });
      });
    },
    handleChangeStatus(event, objectId, objectStatus){
      this.currentAffiliate.status = objectStatus;
      affiliateResource
        .changeStatus(objectId, this.currentAffiliate)
        .then(response => {
          if (objectStatus === 'S'){
            this.$message({
              message: 'Tipo de afiliado id (' + objectId + ') habilitado.',
              type: 'success',
              duration: 5 * 1000,
            });
          } else {
            this.$message({
              message: 'Tipo de afiliado id (' + objectId + ') desabilitado.',
              type: 'warning',
              duration: 5 * 1000,
            });
          }
        })
        .catch(error => {
          console.log(error);
          this.getList();
        });
    },
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['id', 'tpAffiliate_id', 'corporate_name'];
        const filterVal = ['index', 'id', 'corporate_name'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'affiliate-list',
        });
        this.downloading = false;
      });
    },
    handleSearchZipCodeBrazil: function(zipcode) {
      if (validateZipCode(zipcode)) {
        this.searchZipCodeBrazil(zipcode);
      } else {
        this.$message({
          message: 'CEP inválido',
          type: 'error',
          duration: 5 * 1000,
        });
      }
    },
    createAffiliate() {
      this.$refs['affiliateFormCreate'].validate((valid) => {
        if (valid) {
          this.newAffiliate.roles = [this.newAffiliate.role];
          this.AffiliateCreating = true;
          affiliateResource
            .store(this.newAffiliate)
            .then(response => {
              this.$message({
                message: 'Novo tipo de afiliado ' + this.newAffiliate.name + ' cadastrado com sucesso.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewAffiliate();
              this.dialogFormCreateVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.AffiliateCreating = false;
            });
        } else {
          console.log('Erro ao cadastrar!!');
          return false;
        }
      });
    },
    editAffiliate(id){
      this.$refs['affiliateFormEdit'].validate((valid) => {
        if (valid) {
          this.affiliateEditing = true;
          affiliateResource
            .update(id, this.updateAffiliate)
            .then(response => {
              this.$message({
                message: 'Tipo de afiliado id (' + this.updateAffiliate.id + ') atualizado com sucesso.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.updateAffiliate = {};
              this.dialogFormEditVisible = false;
              this.getList();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.affiliateEditing = false;
            });
        } else {
          console.log('Erro ao editar!!');
          return false;
        }
      });
    },
    resetNewAffiliate() {
      this.newAffiliate = {
        type_person: 'PF',
        corporate_name: '',
        fantasy_name: '',
        street: '',
        number: '',
        district: '',
      };
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => v[j]));
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  // margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
.el-form-item__label{
  text-align: left;
  min-width: 125px;
}
</style>
