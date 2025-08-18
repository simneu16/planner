self.addEventListener('push', function(event) {
    const data = event.data.json();
  
    event.waitUntil(
      self.registration.showNotification(data.title, {
        body: data.body,
        icon: '../assets/logo.jpg',
        data: { url: '../events.php' }
      })
    );
  });
  
  self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
      clients.openWindow(event.notification.data.url)
    );
  });


  console.log('Notification' in window);
  console.log('PushManager' in window);
  console.log('serviceWorker' in navigator);