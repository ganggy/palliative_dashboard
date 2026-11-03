require('dotenv').config();
const express = require('express');
const cors = require('cors');
const path = require('path');
const session = require('express-session');
const cookieParser = require('cookie-parser');
const { testConnection } = require('./config/db');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));
app.use(cookieParser());

// Session
app.use(session({
    secret: process.env.SESSION_SECRET || 'palliative-secret-key-2026',
    resave: false,
    saveUninitialized: false,
    cookie: {
        maxAge: 1000 * 60 * 60 * 8, // 8 hours
        httpOnly: true,
        secure: false // true if HTTPS
    }
}));

// Static files
app.use(express.static(path.join(__dirname, 'public')));

// Routes
const authRoutes = require('./routes/auth');
const apiRoutes = require('./routes/api');

app.use('/api/auth', authRoutes);
app.use('/api', apiRoutes);

// Fallback for single page app
app.use((req, res) => {
    // If request starts with /api return 404
    if (req.path.startsWith('/api')) {
        return res.status(404).json({ success: false, message: 'Endpoint not found' });
    }
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Start Server
app.listen(PORT, async () => {
    console.log(`====================================================`);
    console.log(`  Palliative Care Information System (Node.js)`);
    console.log(`  Khoksrisuphan Hospital`);
    console.log(`  Server running at: http://localhost:${PORT}`);
    console.log(`====================================================`);
    await testConnection();
});
