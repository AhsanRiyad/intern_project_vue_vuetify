

<template>
  <v-app class="grey"> 

  <!-- 
    <component v-bind:is="$store.getters.getComponentName"> <buttons_for_profile></buttons_for_profile> </component> -->
    
    <v-container>

      <v-row>
        <v-col cols="12" md="12" class="mt-n6 mb-md-n6">
        <keep-alive>
          <component v-bind:is="$store.getters.getComponentNameAdminOptions"> 

            <buttons_for_admin_options slot="buttons"></buttons_for_admin_options>

          </component>
          </keep-alive>


          
          <!-- <profile_basic></profile_basic> -->
        </v-col>
      </v-row>
    </v-container>

  </v-app>
</template>
<script>
// @ is an alias to /src

import buttons_for_admin_options from '@/views/admin_options/buttons_for_admin_options'
import institution_id_label from '@/views/admin_options/institution_id_label'
import form_field_rules from '@/views/admin_options/form_field_rules'
import import_user_data from '@/views/admin_options/import_user_data'


export default {
  name: 'admin_options',
  components: { 
  'buttons_for_admin_options': buttons_for_admin_options , 
  'institution_id_label': institution_id_label , 
  'form_field_rules': form_field_rules, 
  'import_user_data': import_user_data

}, 
  data: ()=>({}), 
  created(){



  },
  methods: {},
  submit(){},

}
</script>

<style>
</style>