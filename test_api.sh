#!/bin/bash

BASE_URL="http://localhost:8000/api"

echo "=== Testing Laravel Blog API ==="
echo ""

# Test 1: Register a new user
echo "1. Testing User Registration..."
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "testuser@example.com",
    "password": "TestPassword123!",
    "password_confirmation": "TestPassword123!"
  }')

echo "Response: $REGISTER_RESPONSE"
TOKEN=$(echo $REGISTER_RESPONSE | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
echo "Token extracted: $TOKEN"
echo ""

# Test 2: Login
echo "2. Testing User Login..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "testuser@example.com",
    "password": "TestPassword123!"
  }')

echo "Response: $LOGIN_RESPONSE"
LOGIN_TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
echo "Login Token: $LOGIN_TOKEN"
echo ""

# Test 3: Get authenticated user
echo "3. Testing Get Authenticated User..."
curl -s -X GET "$BASE_URL/auth/me" \
  -H "Authorization: Bearer $LOGIN_TOKEN" | jq .
echo ""

# Test 4: Get all posts
echo "4. Testing Get All Posts..."
curl -s -X GET "$BASE_URL/posts" \
  -H "Authorization: Bearer $LOGIN_TOKEN" | jq '.data | .[0:2]'
echo ""

# Test 5: Create a new post
echo "5. Testing Create Post..."
POST_RESPONSE=$(curl -s -X POST "$BASE_URL/posts" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -d '{
    "title": "My Test Post",
    "content": "This is a test post created via API",
    "status": "published"
  }')

echo "Response: $POST_RESPONSE"
POST_ID=$(echo $POST_RESPONSE | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
echo "Post ID: $POST_ID"
echo ""

# Test 6: Get single post
echo "6. Testing Get Single Post..."
curl -s -X GET "$BASE_URL/posts/$POST_ID" \
  -H "Authorization: Bearer $LOGIN_TOKEN" | jq .
echo ""

# Test 7: Create a comment
echo "7. Testing Create Comment..."
COMMENT_RESPONSE=$(curl -s -X POST "$BASE_URL/comments" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -d "{
    \"post_id\": $POST_ID,
    \"content\": \"This is a test comment\"
  }")

echo "Response: $COMMENT_RESPONSE"
COMMENT_ID=$(echo $COMMENT_RESPONSE | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
echo "Comment ID: $COMMENT_ID"
echo ""

# Test 8: Get all comments
echo "8. Testing Get All Comments..."
curl -s -X GET "$BASE_URL/comments" \
  -H "Authorization: Bearer $LOGIN_TOKEN" | jq '.data | .[0:2]'
echo ""

# Test 9: Update post
echo "9. Testing Update Post..."
curl -s -X PUT "$BASE_URL/posts/$POST_ID" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -d '{
    "title": "Updated Test Post",
    "content": "This post has been updated"
  }' | jq .
echo ""

# Test 10: Update comment
echo "10. Testing Update Comment..."
curl -s -X PUT "$BASE_URL/comments/$COMMENT_ID" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -d '{
    "content": "This comment has been updated"
  }' | jq .
echo ""

# Test 11: Logout
echo "11. Testing Logout..."
curl -s -X POST "$BASE_URL/auth/logout" \
  -H "Authorization: Bearer $LOGIN_TOKEN" | jq .
echo ""

# Test 12: Refresh token
echo "12. Testing Refresh Token..."
curl -s -X POST "$BASE_URL/auth/refresh-token" \
  -H "Authorization: Bearer $LOGIN_TOKEN" | jq .
echo ""

echo "=== All tests completed ==="
