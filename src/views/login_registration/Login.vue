<template>
	<v-app class="grey" > 
		<v-container class="white" >
			<v-row justify="center" align="center" class="my-auto"> 
				<v-col cols="8" xl="4" >
					
					
					<v-form
					ref="form"
					v-model="valid"
					:lazy-validation="lazy"
					v-on:submit.prevent
					>



					<h3> Welcome, Please Login! </h3>

					<v-text-field
					@keyup.enter="submit()"
					v-model="email"
					label="Email"
					type="text"
					:rules="[ 
					v => !!v || 'required',
					]"
					@change="getUserDetails()"
					></v-text-field>


					<v-text-field
					@keyup.enter="submit"
					v-model="password"
					type="password"
					label="password"
					:rules="[ v => !!v || 'required' ]"
					></v-text-field>

					<v-btn
					color="success"
					class="mr-4"
					@click="submit"
					:loading="loading"
					>
					Login
				</v-btn>


			</v-form>


		</v-col>
	</v-row>

	<v-row justify="center" align="center">

		<v-col cols="8" xl="4" >
			<v-btn 
			router :to="{ name : 'registration' }"
			color="primary"
			class="mr-4 mb-2 mb-sm-0"
			>
			Registration
		</v-btn>


		<v-btn 
		router :to="{ name : 'profile_forgot_password' }"
		color="warning"
		class="mr-4 "
		>
		Forgot Password
	</v-btn>
	<noInternetSnackBar ref="snackbar" ></noInternetSnackBar>

</v-col>

</v-row>

</v-container>

<v-row justify="center">
	<v-dialog
	v-model="dialog"
	max-width="290"
	>
	<v-card>
		<v-card-title class="headline">Status</v-card-title>

		<v-card-text>
			{{ login_status }}
		</v-card-text>

		<v-card-actions>
			<v-spacer></v-spacer>

			

			<v-btn
			color="green darken-1"
			text
			@click="dialog = false"
			>
			Close
		</v-btn>
	</v-card-actions>
</v-card>
</v-dialog>
</v-row>






</v-app>
</template>

<script>
// @ is an alias to /src
// import { bus } from '@/main'
import cookie_mixins from '@/mixins/cookie_mixins'
import noInternetSnackBar from '@/views/noInternetSnackBar'
const md5 = require('crypto-js/md5');
export default {
	name: 'login',
	mixins: [cookie_mixins],
	components: { 'noInternetSnackBar': noInternetSnackBar },
	data: ()=>({
		loading: false,
		dialog: false,
		status_text: '',
		email: '',
		password: '',
		login_status: '',
		greenText: false,
		redText: false,
		date : new Date(),
		expires: '',
		lazy:false,
		valid:false,

	}), 


	methods: {
		getUserDetails(resolve){
			//this.expires = this.date.setTime(this.date.getTime() + 7*24*60*60*1000).toGMTString();
			var headers = {
				'Content-Type': 'application/x-www-form-urlencoded',
				'Accept': 'application/json'} ;


				this.$axios.post( this.$store.getters.modelProfile_basic , {
					purpose : 'getProfileBasicInfoForAuth',
					email: this.email , 
				} ,

				headers


				)
				.then(function(response){

					console.log(response);
					
					
					if(response.data.userInfo !=0){
						this.$store.commit('set_user_info' , response.data.userInfo);

						response.data.userInfo.status == 'approved' ? this.$store.commit('verifiedTrue') :  this.$store.commit('verifiedFalse');

					}

					this.$store.commit('setIInstitution_id_label' , response.data.institution_id_label);

					this.$store.commit('setVerificationRequest' , response.data.countRequest.verificationRequest);

					this.$store.commit('setChangeRequest' , response.data.countRequest.changeRequest);

					// this.$store.commit('set_user_info' , response.data);
						//this.$store.commit('loginTrue');

						// this.$router.push({ name: 'profile' }) ;

						resolve != undefined ? resolve() : '';


					}.bind(this))
				.catch(function () {
					
				// this.$store.mutations('noInternetSnackBar');
				this.$refs.snackbar.startSnackBar();

			}.bind(this));

			},
			checkPasswordAndLogin(){




				

				this.loading = true;
				if( this.$refs.form.validate() ){
					if(md5(this.password) == this.$store.getters.getAllInfo.password){ 

				/*this.setCookie('email' , this.email , 7);
				this.setCookie('crypto' , this.$store.getters.getAllInfo.forgot_password_crypto , 7);*/
				this.$cookies.set('email' , this.email);
				this.$cookies.set('crypto' , this.$store.getters.getAllInfo.forgot_password_crypto);

				this.$store.commit('loginTrue');
				
				this.$store.getters.getAllInfo.type == 'admin' ? this.$store.commit('adminTrue') :  '';

				this.$store.getters.getAllInfo.email_verification_status == 'verified' ? this.$store.commit('emailVerifiedTrue') :  this.$store.commit('emailVerifiedFalse')   ;




				this.loading = false;
				this.$store.commit('setPageTitle' , 'Dashboard');
				this.$router.push({ name: 'profile' }) ;
				// this.$router.push({ name: 'registration' }) ;
				// this.$router.replace('profile') ;
			}else{
				this.loading = false;
				this.login_status = 'email/password doesnt match';
				this.dialog = true;
			}
		}else{
			this.login_status = 'invalid field detected';
			this.dialog = true;
			this.loading = false; 
		}



	},

	submit(){



		if(this.$store.getters.getAllInfo == '' || this.$store.getters.getAllInfo == 0 || this.$store.getters.getAllInfo == undefined ){
			
			let promise = new Promise((resolve)=>{

				this.getUserDetails(resolve);

			});
			promise.then(()=>{
				this.checkPasswordAndLogin();

			})

		}else{
			
			this.checkPasswordAndLogin();

		}



	}
},
created(){

	this.$store.commit('set_user_info' , '');
	this.$store.commit('loginFalse');
	this.$store.commit('adminFalse');
	this.$cookies.set('email', '');
	this.$cookies.set('crypto', '');







	// this.getCommonInfo();



},
mounted(){},
updated(){

	//this.$store.commit('set_user_info' , '');

	this.$store.commit('loginFalse');
	this.$store.commit('adminFalse');
	this.$cookies.set('email', '');
	this.$cookies.set('crypto', '');


	// this.getCommonInfo();



}
}
</script>
