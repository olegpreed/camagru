# Camagru - Web Application Project

## Phase 1: Docker Setup

### Prerequisites
- Docker Desktop installed
- Docker Compose installed

### Getting Started

1. Copy `.env.example` to `.env`:
   cp .env.example .env
   2. Build and start containers:
  
   docker-compose up -d --build
   3. Access the application:
   - Open browser: http://localhost:8080

4. Stop containers:
   docker-compose down
   5. View logs:
   
   docker-compose logs -f
   ### Current Status
- ✅ Docker Compose setup
- ✅ PHP 8.2 with Apache
- ✅ MySQL 8.0
- ✅ Basic project structure

### Next Phase
Phase 2: Database Design & Setup