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

      <el-table-column align="center" label="Nome">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Ações" width="350">
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

    <el-dialog :title="'Cadastrar novo tipo de affiliado'" :visible.sync="dialogFormCreateVisible">
      <div v-loading="tpAffiliateCreating" class="form-container">
        <el-form ref="tpAffiliateFormCreate" :rules="rules" :model="newTpAffiliate" style="max-width: 500px;">
          <el-form-item :label="$t('nome')" prop="name">
            <el-input v-model="newTpAffiliate.name" />
          </el-form-item>
          <el-form-item :label="$t('Descrição')" prop="description">
            <el-input v-model="newTpAffiliate.description" type="textarea" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormCreateVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createTpAffiliate()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog :title="'Editar tipo de affiliado id ('+ updateTpAffiliate.id +')'" :visible.sync="dialogFormEditVisible" @close="getList">
      <div v-loading="tpAffiliateEditing" class="form-container">
        <el-form ref="tpAffiliateFormEdit" :rules="rules" :model="updateTpAffiliate" label-position="left" style="max-width: 500px;">
          <el-form-item :label="$t('nome')" prop="name">
            <el-input v-model="updateTpAffiliate.name" />
          </el-form-item>
          <el-form-item :label="$t('Descrição')" prop="description">
            <el-input v-model="updateTpAffiliate.description" type="textarea" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="editTpAffiliate(updateTpAffiliate.id)">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>

import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import TpaffiliateResource from '@/api/master/tpaffiliate'; // class api tpaffiliate
import waves from '@/directive/waves'; // Waves directive

const tpaffiliateResource = new TpaffiliateResource();

export default {
  name: 'TpAffiliateList',
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      list: null,
      listOld: null,
      total: 0,
      loading: true,
      downloading: false,
      tpAffiliateCreating: false,
      tpAffiliateEditing: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        status: '',
      },
      newTpAffiliate: {},
      updateTpAffiliate: {},
      dialogFormCreateVisible: false,
      dialogFormEditVisible: false,
      //
      currentTpAffiliate: {
        id: 0,
        status: null,
        name: '',
        description: '',
      },
      rules: {
        name: [
          { required: true, message: 'Nome está vazio', trigger: 'blur' },
          { min: 4, message: 'Nome deve conter no mínimo 4 caracteres', trigger: ['blur', 'change'] },
        ],
      },
    };
  },
  computed: {
  },
  created() {
    this.resetNewTpAffiliate();
    this.getList();
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await tpaffiliateResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleCreate() {
      this.resetNewTpAffiliate();
      this.dialogFormCreateVisible = true;
      this.$nextTick(() => {
        this.$refs['tpAffiliateFormCreate'].clearValidate();
      });
    },
    handleEdit(id) {
      this.updateTpAffiliate = {};
      this.listOld = this.list;
      this.updateTpAffiliate = this.list.filter(tpAffiliateFiltered => tpAffiliateFiltered.id === id);
      // this.updateTpAffiliate = Object.values(this.list).filter(tpAffiliateFiltered => tpAffiliateFiltered.id === id);
      this.updateTpAffiliate = this.updateTpAffiliate[0];
      this.list = this.listOld;
      this.dialogFormEditVisible = true;
      this.$nextTick(() => {
        this.$refs['tpAffiliateFormEdit'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('Confirma excluir o tipo de afiliado ' + name + '. Confirmar?', 'Warning', {
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }).then(() => {
        tpaffiliateResource.destroy(id).then(response => {
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
      this.currentTpAffiliate.status = objectStatus;
      tpaffiliateResource
        .changeStatus(objectId, this.currentTpAffiliate)
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
        const tHeader = ['id', 'tpAffiliate_id', 'name', 'description'];
        const filterVal = ['index', 'id', 'name', 'description'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'tpAffiliate-list',
        });
        this.downloading = false;
      });
    },
    createTpAffiliate() {
      this.$refs['tpAffiliateFormCreate'].validate((valid) => {
        if (valid) {
          this.newTpAffiliate.roles = [this.newTpAffiliate.role];
          this.tpAffiliateCreating = true;
          tpaffiliateResource
            .store(this.newTpAffiliate)
            .then(response => {
              this.$message({
                message: 'Novo tipo de afiliado ' + this.newTpAffiliate.name + ' cadastrado com sucesso.',
                type: 'success',
                duration: 5 * 1000,
              });
              console.log(response);
              this.resetNewTpAffiliate();
              this.dialogFormCreateVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.tpAffiliateCreating = false;
            });
        } else {
          console.log('Erro ao cadastrar!!');
          return false;
        }
      });
    },
    editTpAffiliate(id){
      this.$refs['tpAffiliateFormEdit'].validate((valid) => {
        if (valid) {
          this.tpAffiliateEditing = true;
          tpaffiliateResource
            .update(id, this.updateTpAffiliate)
            .then(response => {
              this.$message({
                message: 'Tipo de afiliado id (' + this.updateTpAffiliate.id + ') atualizado com sucesso.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.updateTpAffiliate = {};
              this.dialogFormEditVisible = false;
              this.getList();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.tpAffiliateEditing = false;
            });
        } else {
          console.log('Erro ao editar!!');
          return false;
        }
      });
    },
    resetNewTpAffiliate() {
      this.newTpAffiliate = {};
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
@media only screen and (max-width: 575px) {
  .el-dialog {
    width: 90%;
  }
  .app-container {
    padding: 40px;
  }
}
</style>
