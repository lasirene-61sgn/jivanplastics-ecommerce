@props(['role' => 'admin']) <!-- Can be 'admin' or 'manufacturing' based on who is logged in -->

<!-- SweetAlert2 for Toast Notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
  import { getDatabase, ref, onChildAdded, query, orderByChild, startAt } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-database.js";

  // Your web app's Firebase configuration (Make sure this matches your project exactly)
  const firebaseConfig = {
    apiKey: "AIzaSyDGUWXHP_6zM9CTfIn8hdvUq2_4RRD6V9s",
    authDomain: "jivan-e770c.firebaseapp.com",
    projectId: "jivan-e770c",
    storageBucket: "jivan-e770c.firebasestorage.app",
    messagingSenderId: "384682822037",
    appId: "1:384682822037:web:f07c1ea93253f110c68e9c",
    measurementId: "G-NM950Y6L4J"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const database = getDatabase(app);

  // We only want to listen to notifications created AFTER the user loads the page
  const currentTime = Math.floor(Date.now() / 1000); 

  // Which topic/role are we listening to? (passed via Blade component prop)
  const topic = "{{ $role }}";
  const notificationsRef = ref(database, 'notifications/' + topic);
  
  // Create a query to only get new notifications
  const newNotificationsQuery = query(notificationsRef, orderByChild('timestamp'), startAt(currentTime));

  console.log(`Listening for real-time Firebase notifications on topic: ${topic}...`);

  // Listen for new child added
  onChildAdded(newNotificationsQuery, (snapshot) => {
    const data = snapshot.val();
    
    // Show Toast Notification
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 5000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });

    Toast.fire({
      icon: data.icon || "info", // Can pass 'success', 'warning', 'info' from backend
      title: data.title,
      text: data.message
    });
  });
</script>
