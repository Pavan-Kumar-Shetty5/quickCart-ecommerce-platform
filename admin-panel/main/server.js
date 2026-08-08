const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);

// Enable CORS so your PHP/XAMPP site can connect to this Node server
const io = new Server(server, {
    cors: {
        origin: "http://localhost", // Adjust if your XAMPP uses a different port like http://localhost:8080
        methods: ["GET", "POST"]
    }
});

// Store active chat users
const activeUsers = new Map();

io.on('connection', (socket) => {
    console.log('New connection established:', socket.id);

    socket.on('join chat', (data) => {
        socket.userId = data.userId;
        socket.role = data.role;

        if (data.role === 'admin') {
            socket.join('admin_room');
            console.log("Admin joined the control room");
            
            // 1. Tell Admin who is currently online
            const usersArray = Array.from(activeUsers.values());
            socket.emit('active chats list', usersArray);
        } else {
            // 2. Add User to the map
            activeUsers.set(data.userId, { 
                userId: data.userId, 
                socketId: socket.id, 
                messageCount: 0 
            });
            console.log(`User joined: ${data.userId}`);

            // 3. Update the Admin's sidebar immediately
            io.to('admin_room').emit('active chats list', Array.from(activeUsers.values()));
        }
    });

    socket.on('user message', (data) => {
        console.log(`Message from ${data.userId}: ${data.text}`);
        
        // Update message count for this user
        if(activeUsers.has(data.userId)) {
            activeUsers.get(data.userId).messageCount++;
        }

        // Send to Admin using the event name your HTML expects: 'new user message'
        io.to('admin_room').emit('new user message', {
            userId: data.userId,
            text: data.text,
            timestamp: new Date()
        });
    });

    socket.on('admin reply', (data) => {
        console.log(`Admin reply to ${data.targetUserId}: ${data.text}`);

        // Send specifically to the user's side
        io.emit('message to user', {
            sender: 'Admin',
            text: data.text,
            targetUserId: data.targetUserId
        });
    });

    socket.on('disconnect', () => {
        if (socket.role !== 'admin') {
            activeUsers.delete(socket.userId);
            io.to('admin_room').emit('active chats list', Array.from(activeUsers.values()));
        }
        console.log('User disconnected:', socket.userId);
    });
});

// Use port 3000 so it doesn't conflict with XAMPP (Port 80)

const PORT = 3000;
server.listen(PORT, () => {
    console.log(`-----------------------------------------------`);
    console.log(`QuickCart Chat Server is Running!`);
    console.log(`Port: ${PORT}`);
    console.log(`Ready for PHP connections from XAMPP`);
    console.log(`-----------------------------------------------`);
});