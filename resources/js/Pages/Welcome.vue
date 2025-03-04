<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const form = useForm({
    first_name: 'f',
    last_name: 'l',
    email: 'a@gmail.com',
    phone: '0901232123',
    check_in_date: '2025-03-04',
    check_out_date: '2025-03-05',
    total_male: 1,
    total_female: 2,
});

function submit() {
    console.log(1);
    form.post(route('reservation.create'));
}
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen">
        <div>
            <Link
                :href="route('login')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
            >
                Log in
            </Link>

            <Link
                v-if="canRegister"
                :href="route('register')"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
            >
                Register
            </Link>
        </div>

        <main>
            <form @submit.prevent="submit">
                <div>
                    <label>First Name:</label>
                    <input type="text" v-model="form.first_name" />
                </div>
                <div>
                    <label>Last Name:</label>
                    <input type="text" v-model="form.last_name" />
                </div>
                <div>
                    <label>Email:</label>
                    <input type="email" v-model="form.email" />
                </div>
                <div>
                    <label>Phone:</label>
                    <input type="text" v-model="form.phone" />
                </div>
                <div>
                    <label>Check In:</label>
                    <input type="date" v-model="form.check_in_date" />
                </div>
                <div>
                    <label>Check Out:</label>
                    <input type="date" v-model="form.check_out_date" />
                </div>
                <div>
                    <label>Total Males:</label>
                    <input type="number" v-model.number="form.total_male" />
                </div>
                <div>
                    <label>Total Females:</label>
                    <input type="number" v-model.number="form.total_female" />
                </div>
                <button type="submit">Submit</button>
            </form>
            {{ form.errors }}
        </main>
    </div>
</template>
