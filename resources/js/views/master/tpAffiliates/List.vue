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
          <span>{{ scope.row.index }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Status">
        <template slot-scope="scope">
          <el-switch v-model="scope.row.status" active-color="#13ce66" inactive-color="#ff4949">
          </el-switch>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Name">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Description">
        <template slot-scope="scope">
          <span>{{ scope.row.description }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="350">
        <template slot-scope="scope">
          <el-button v-permission="['manage user']" type="danger" size="small" icon="el-icon-delete" @click="handleDelete(scope.row.id, scope.row.name);">
            Delete
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />

    <el-dialog :title="'Cadastrar novo tipo de affiliado'" :visible.sync="dialogFormVisible">
      <div v-loading="tpAffiliateCreating" class="form-container">
        <el-form ref="tpAffiliateForm"  :model="newTpAffiliate" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="$t('tpAffiliate.name')" prop="name">
            <el-input v-model="newTpAffiliate.name" />
          </el-form-item>
          <el-form-item :label="$t('tpAffiliate.description')" prop="email">
            <el-input v-model="newTpAffiliate.description" type="textarea" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createTpAffiliate()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>

import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import TpaffiliateResource from '@/api/master/tpaffiliate';
// import Resource from '@/api/resource';
import waves from '@/directive/waves'; // Waves directive
// import permission from '@/directive/permission'; // Permission directive
// import checkPermission from '@/utils/permission'; // Permission checking

const tpaffiliateResource = new TpaffiliateResource();
// const permissionResource = new Resource('permissions');

export default {
  name: 'TpAffiliateList',
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      list: null,
      total: 0,
      loading: true,
      downloading: false,
      tpAffiliateCreating: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
      },
      newTpAffiliate: {},
      dialogFormVisible: false,
      currentTpAffiliateId: 0,
      //
      currentTpAffiliate: {
        name: '',
      },
      rules: {
        name: [{ required: true, message: 'Name is required', trigger: 'blur' }],
        description: [{ required: true, message: 'Description is required', trigger: 'blur' }],
      },
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
    };
  },
  computed: {
    userMenuPermissions() {
      return this.classifyPermissions(this.userPermissions).menu;
    },
    userOtherPermissions() {
      return this.classifyPermissions(this.userPermissions).other;
    },
    userPermissions() {
      return this.currentTpAffiliate.permissions.role.concat(this.currentTpAffiliate.permissions.user);
    },
  },
  created() {
    this.resetNewTpAffiliate();
    this.getList();
    // if (checkPermission(['manage permission'])) {
    //   this.getPermissions();
    // }
  },
  methods: {
    // checkPermission,
    // async getPermissions() {
    // const { data } = await permissionResource.list({});
    //  const { all, menu, other } = this.classifyPermissions(data);
    //  this.permissions = all;
    //  this.menuPermissions = menu;
    //  this.otherPermissions = other;
    // },

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
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['tpAffiliateForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('This will permanently delete tipo de afiliado ' + name + '. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        tpaffiliateResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: 'Delete completed',
          });
          this.handleFilter();
        }).catch(error => {
          console.log(error);
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Delete canceled',
        });
      });
    },
    createTpAffiliate() {
      this.$refs['tpAffiliateForm'].validate((valid) => {
        if (valid) {
          this.newTpAffiliate.roles = [this.newTpAffiliate.role];
          this.tpAffiliateCreating = true;
          tpaffiliateResource
            .store(this.newTpAffiliate)
            .then(response => {
              this.$message({
                message: 'New tipo de afiliado ' + this.newTpAffiliate.name + 'has been created successfully.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewTpAffiliate();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.tpAffiliateCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetNewTpAffiliate() {
      this.newTpAffiliate = {
        name: '',
        description: '',
      };
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
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => v[j]));
    },
    strToBool(string){
      return (string === 'S');
    },
    // permissionKeys(permissions) {
    //   return permissions.map(permssion => permssion.id);
    // },
    // classifyPermissions(permissions) {
    //   const all = []; const menu = []; const other = [];
    //   permissions.forEach(permission => {
    //     const permissionName = permission.name;
    //     all.push(permission);
    //     if (permissionName.startsWith('view menu')) {
    //       menu.push(this.normalizeMenuPermission(permission));
    //     } else {
    //       other.push(this.normalizePermission(permission));
    //     }
    //   });
    //   return { all, menu, other };
    // },
    /*
    normalizeMenuPermission(permission) {
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name.substring(10)), disabled: permission.disabled || false };
    },

    normalizePermission(permission) {
      const disabled = permission.disabled || permission.name === 'manage permission';
      return { id: permission.id, name: this.$options.filters.uppercaseFirst(permission.name), disabled: disabled };
    },

    confirmPermission() {
      const checkedMenu = this.$refs.menuPermissions.getCheckedKeys();
      const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
      const checkedPermissions = checkedMenu.concat(checkedOther);
      this.dialogPermissionLoading = true;

      userResource.updatePermission(this.currentTpAffiliateId, { permissions: checkedPermissions }).then(response => {
        this.$message({
          message: 'Permissions has been updated successfully',
          type: 'success',
          duration: 5 * 1000,
        });
        this.dialogPermissionLoading = false;
        this.dialogPermissionVisible = false;
      });
    },*/
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
  margin-left: 150px;
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
</style>
