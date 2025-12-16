#!/bin/bash

BASE_URL="http://localhost:8000/api"

echo "=== Testing Token Invalidation on Login ==="
echo ""

# Test 1: Register a new user
echo "1. Registering a new user..."
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Token Test User",
    "email": "tokentest@example.com",
    "password": "TestPassword123!",
    "password_confirmation": "TestPassword123!"
  }')

echo "Response: $REGISTER_RESPONSE"
FIRST_TOKEN=$(echo $REGISTER_RESPONSE | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
echo "First Token: $FIRST_TOKEN"
echo ""

# Test 2: Verify first token works
echo "2. Testing that the first token is valid..."
FIRST_TOKEN_TEST=$(curl -s -X GET "$BASE_URL/auth/me" \
  -H "Authorization: Bearer $FIRST_TOKEN")

echo "Response: $FIRST_TOKEN_TEST"
FIRST_TOKEN_SUCCESS=$(echo $FIRST_TOKEN_TEST | grep -o '"success":true' | head -1)

if [ -z "$FIRST_TOKEN_SUCCESS" ]; then
  echo "ERROR: First token should be valid!"
  exit 1
fi

echo "SUCCESS: First token is valid"
echo ""

# Test 3: Login again with the same user (this should invalidate the first token)
echo "3. Logging in again with the same user (this should invalidate the first token)..."
SECOND_LOGIN=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "tokentest@example.com",
    "password": "TestPassword123!"
  }')

echo "Response: $SECOND_LOGIN"
SECOND_TOKEN=$(echo $SECOND_LOGIN | grep -o '"access_token":"[^"]*' | cut -d'"' -f4)
echo "Second Token: $SECOND_TOKEN"
echo ""

# Test 4: Verify second token works
echo "4. Testing that the second token is valid..."
SECOND_TOKEN_TEST=$(curl -s -X GET "$BASE_URL/auth/me" \
  -H "Authorization: Bearer $SECOND_TOKEN")

echo "Response: $SECOND_TOKEN_TEST"
SECOND_TOKEN_SUCCESS=$(echo $SECOND_TOKEN_TEST | grep -o '"success":true' | head -1)

if [ -z "$SECOND_TOKEN_SUCCESS" ]; then
  echo "ERROR: Second token should be valid!"
  exit 1
fi

echo "SUCCESS: Second token is valid"
echo ""

# Test 5: Verify that the first token is now INVALID (this is the critical test)
echo "5. Testing that the FIRST token is now INVALID (should be revoked)..."
FIRST_TOKEN_INVALID_TEST=$(curl -s -X GET "$BASE_URL/auth/me" \
  -H "Authorization: Bearer $FIRST_TOKEN")

echo "Response: $FIRST_TOKEN_INVALID_TEST"
FIRST_TOKEN_INVALID=$(echo $FIRST_TOKEN_INVALID_TEST | grep -o '"success":false' | head -1)

if [ -z "$FIRST_TOKEN_INVALID" ]; then
  echo "ERROR: First token should have been revoked!"
  echo "First token is still valid - SECURITY ISSUE!"
  exit 1
fi

echo "SUCCESS: First token has been properly revoked!"
echo ""

# Test 6: Try to use first token to create a post (should fail)
echo "6. Testing that the FIRST token cannot be used to create a post..."
FIRST_TOKEN_POST_TEST=$(curl -s -X POST "$BASE_URL/posts" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $FIRST_TOKEN" \
  -d '{
    "title": "Test Post",
    "content": "This should fail",
    "status": "published"
  }')

echo "Response: $FIRST_TOKEN_POST_TEST"
FIRST_TOKEN_POST_FAILED=$(echo $FIRST_TOKEN_POST_TEST | grep -o '"success":false' | head -1)

if [ -z "$FIRST_TOKEN_POST_FAILED" ]; then
  echo "ERROR: First token should not be able to create a post!"
  exit 1
fi

echo "SUCCESS: First token cannot be used to create a post"
echo ""

# Test 7: Use second token to create a post (should succeed)
echo "7. Testing that the SECOND token CAN be used to create a post..."
SECOND_TOKEN_POST_TEST=$(curl -s -X POST "$BASE_URL/posts" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $SECOND_TOKEN" \
  -d '{
    "title": "Test Post with Second Token",
    "content": "This should succeed",
    "status": "published"
  }')

echo "Response: $SECOND_TOKEN_POST_TEST"
SECOND_TOKEN_POST_SUCCESS=$(echo $SECOND_TOKEN_POST_TEST | grep -o '"success":true' | head -1)

if [ -z "$SECOND_TOKEN_POST_SUCCESS" ]; then
  echo "ERROR: Second token should be able to create a post!"
  exit 1
fi

echo "SUCCESS: Second token can be used to create a post"
echo ""

echo "=== All token invalidation tests passed! ==="
echo "The API correctly invalidates the previous token when a user logs in again."
