<template>
	<v-app class="grey" > 
		<v-container class="white" >
			<v-row justify="center" align="center"> 
				<v-col cols="8" xl="4" >

					<v-form
					ref="form"
					v-model="valid"
					:lazy-validation="lazy"
					v-on:submit.prevent
					>


					<h3 > Dear User, Please enter your email, we will send you recovery instructions </h3>


					<v-text-field
					v-model="email"
					label="Email"
					type="text"
					:rules="[ 
					v => !!v || 'required',
					v => /^[a-zA-Z]{1}[a-zA-Z1-9._]{3,15}@[a-zA-Z]{1}[a-zA-Z1-9]{3,15}\.[a-zA-Z]{2,10}(\.[a-zA-Z]{2})*$/g.test(v) || 'invalide quantity'
					]"
					></v-text-field>
					<v-btn
					
					color="success"
					class="mr-4 mt-4"
					@click="submit()"
					:loading="loading"
					>
					Recover
				</v-btn>
			</v-form>
		</v-col>



	</v-row>


	<v-row justify="center" align="center">

		<v-col cols="8" xl="4" >

			<v-btn router :to="{ name: 'login' }"
			color="primary"
			class="mr-4 mb-2 mb-sm-0"
			>
			Login
		</v-btn>

		<v-btn router :to="{ name: 'registration' }"
		color="warning"
		class="mr-4"
		>
		Registration
	</v-btn>




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
export default {
	name: 'profile_forgot_password',
	data: ()=>({
		loading: false,
		status_text: '',
		email: '',
		
		login_status: '',
		dialog: '',


		valid: true, 
		lazy: true,
	}), 


	methods: {
		submit: function(){
				//alert('submit clicked');
				this.invalid_link = '';

				this.loading = true;

				//alert('inside submit');
				if(this.$refs.form.validate()){
					var headers = {
						'Content-Type': 'application/x-www-form-urlencoded',
						'Accept': 'application/json'} ;
						this.$axios.post( this.$store.getters.modelProfile_forgot_password , {
							purpose: 'forgot_password',
							email: this.email,

						} , headers )
						.then( function(response){
							this.loading = false;
							console.log(response);
							this.login_status = response.data;


							if(response.data == 'crypto_added'){
								this.login_status = 'Recovery email sent , please check your email';
								this.dialog = true;
							}else if(response.data == 'no_email_found'){
								this.login_status = 'Email not found';
								this.dialog = true;
							}else if(response.data == 'server_problem'){
								this.login_status = 'Email server problem';
								this.dialog = true;
							}


						}.bind(this))
						.catch(function () {

							this.loading = false;
							this.login_status = 'Server problem';
							this.dialog = true;

                //return 'hi';
            }.bind(this)); 

					}else{
						this.loading = false;
						this.login_status = 'invalid_email';
						this.dialog = true;
					}


				},
				onChangeValidity(inputName){

					if(inputName == 'email'){

						let patt= /^[a-zA-Z]{1}[a-zA-Z1-9._]{3,15}@[a-zA-Z]{1}[a-zA-Z1-9]{3,15}\.[a-zA-Z]{2,10}(\.[a-zA-Z]{2})*$/g;
						let result = patt.test(this.email);

						if(!result){
							let errors = [];
							errors.push('email error');
							this.email_validity = 'invalid'
							return errors;
						}else{
							this.email_validity = 'valid';
						}


					}



				},


			},
			created(){

				this.$store.commit('loginFalse');
				this.$store.commit('adminFalse');
				this.$cookies.set('email', '');
				this.$cookies.set('crypto', '');
			}



		}

	</script>
