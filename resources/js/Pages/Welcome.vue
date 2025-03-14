<template>
    <div class="form-container">

        <div class="spinner-container" v-if="form.processing">
            <div class="spinner"></div>
        </div>

        <form @submit.prevent="submitForm" class="centered-form">
            <h2>Zoho CRM Form</h2>

            <!-- Account Name -->
            <label for="name">Account name:</label>
            <input
                type="text"
                id="name"
                v-model="form.account_name"
                placeholder="Enter your name"
            />
            <div class="error-message" v-if="errors.account_name">{{ errors.account_name[0] }}</div>

            <!-- Account Website -->
            <label for="website">Account website:</label>
            <input
                type="text"
                id="website"
                v-model="form.account_website"

                placeholder="Enter your website"
            />
            <div class="error-message" v-if="errors.account_website">{{ errors.account_website[0] }}</div>

            <!-- Phone -->
            <label for="phone">Phone:</label>
            <input
                type="text"
                id="phone"
                v-model="form.account_phone"

                placeholder="Enter your phone number"
            />
            <div class="error-message" v-if="errors.account_phone">{{ errors.account_phone[0] }}</div>

            <!-- Deal Name -->
            <label for="dealName">Deal name:</label>
            <input
                type="text"
                id="dealName"
                v-model="form.deal_name"
                placeholder="Enter Deal Name"
            />
            <div class="error-message" v-if="errors.deal_name">{{ errors.deal_name[0] }}</div>

            <!-- Deal Stage -->
            <label for="dropdown">Deal stage:</label>
            <select v-model="form.deal_stage" id="dropdown">
                <option value="" disabled selected>Select an option</option>
                <option value="Qualification">Qualification</option>
                <option value="Negotiation/Review">Negotiation/Review</option>
                <option value="Needs Analysis">Needs Analysis</option>
                <option value="Value Proposition">Value Proposition</option>
                <option value="Identify Decision Maker">Identify Decision Maker</option>
                <option value="Proposal/Price Quote">Proposal/Price Quote</option>
            </select>
            <div class="error-message" v-if="errors.deal_stage">{{ errors.deal_stage[0] }}</div>

            <button type="submit">
                <span v-if="form.processing">Submitting...</span>
                <span v-else>Submit</span>
            </button>
        </form>
    </div>
</template>


<script>
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import axios from "axios";

export default {
    setup() {
        const form = useForm({
            account_name: "",
            account_website: "",
            account_phone: "",
            deal_name: "",
            deal_stage: "",
        })

        const errors = ref({})

        const validateForm = () => {
            let hasErrors = false;
            if (!form.account_name && !errors.value.account_name) {
                errors.value.account_name = ["Account name is required"];
                hasErrors = true;
            }
            if (!form.account_website && !errors.value.account_website) {
                errors.value.account_website = ["Account website is required"];
                hasErrors = true;
            }
            if (!form.account_phone && !errors.value.account_phone) {
                errors.value.account_phone = ["Phone number is required"];
                hasErrors = true;
            } else if (form.account_phone && !/^\d+$/.test(form.account_phone)) {
                errors.value.account_phone = ["Phone number can only contain numbers"];
                hasErrors = true;
            }
            if (!form.deal_name && !errors.value.deal_name) {
                errors.value.deal_name = ["Deal name is required"];
                hasErrors = true;
            }
            if (!form.deal_stage && !errors.value.deal_stage) {
                errors.value.deal_stage = ["Deal stage is required"];
                hasErrors = true;
            }

            return !hasErrors;
        };

        const submitForm = async () => {
            if (!validateForm()) return;

            form.processing = true;
            try {
                const response = await axios.post("/api/zoho/create-records", form);

                alert(response.data.message);


                Object.keys(form).forEach(key => {
                    if (typeof form[key] === "string") {
                        form[key] = "";
                    } else if (typeof form[key] === "string") {
                        form[key] = 0;
                    } else if (Array.isArray(form[key])) {
                        form[key] = [];
                    } else if (typeof form[key] === "object" && form[key] !== null) {
                        form[key] = {};
                    }
                });
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    errors.value = error.response.data.errors;
                } else {
                    alert("An error occurred. Please try again.");
                }
            } finally {
                form.processing = false;
            }
        }

        return {
            form,
            errors,
            submitForm,
        }
    }
}
</script>
