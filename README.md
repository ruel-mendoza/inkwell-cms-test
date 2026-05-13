# CMS Portal - Content & Records Management System

A modern, responsive web application built with React for managing articles and records. Features a clean UI with role-based access (Admin/Editor) and real-time data management.

## Features

- **User Authentication**: Secure login system with role-based permissions
- **Dashboard**: Overview of articles and records with key statistics
- **Articles Management**: Create, edit, view, and delete articles with categories and status tracking
- **Records Management**: Manage employee/personnel records with department and status information
- **Search & Filtering**: Advanced search and filter capabilities across all data
- **Responsive Design**: Works seamlessly on desktop and mobile devices

## Demo Credentials

- **Admin**: username: `admin`, password: `admin123`
- **Editor**: username: `editor`, password: `editor123`

## Technologies Used

- **React 18**: Frontend framework with hooks
- **Vite**: Fast build tool and development server
- **JavaScript (ES6+)**: Modern JavaScript features
- **CSS-in-JS**: Inline styling for component-based design

## Installation & Setup

1. **Clone or navigate to the project directory**:
   ```bash
   cd "c:\Users\RUEL\Local Sites\static"
   ```

2. **Install dependencies**:
   ```bash
   npm install
   ```

3. **Start the development server**:
   ```bash
   npm run dev
   ```

4. **Open your browser** and navigate to `http://localhost:5173/`

## Usage

1. Log in using one of the demo credentials above
2. Navigate between Dashboard, Articles, and Records using the sidebar
3. On the Articles page: Create new articles, edit existing ones, search and filter by status
4. On the Records page: Manage personnel records with department filtering
5. Use the Dashboard for a quick overview of your content

## Building for Production

To create a production build:

```bash
npm run build
```

The built files will be in the `dist` folder.

## Project Structure

```
├── src/
│   └── main.jsx          # App entry point
├── management-system.jsx # Main React component
├── index.html            # HTML template
├── vite.config.js        # Vite configuration
└── package.json          # Dependencies and scripts
```

## Contributing

This is a demo project. Feel free to fork and modify for your own use cases.

## License

MIT License - feel free to use this project for learning or commercial purposes.