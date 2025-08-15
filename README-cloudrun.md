# Deploy MTK-booking to Google Cloud Run with Cloud SQL (MySQL)

## 1. Build and Push Docker Image
```
gcloud builds submit --tag gcr.io/[PROJECT_ID]/mtk-booking
```

## 2. Create Cloud SQL (MySQL) Instance
- Go to Google Cloud Console > SQL > Create Instance
- Choose MySQL, set root password, and create a database for your app

## 3. Set Up Cloud SQL Proxy (Cloud Run Connection)
- When deploying, use the `--add-cloudsql-instances` flag
- Set environment variables for DB connection (see `cloudsql.env.example`)

## 4. Deploy to Cloud Run
```
gcloud run deploy mtk-booking \
  --image gcr.io/[PROJECT_ID]/mtk-booking \
  --add-cloudsql-instances=[PROJECT_ID]:[REGION]:[INSTANCE_NAME] \
  --set-env-vars DB_HOST=127.0.0.1,DB_NAME=your_db_name,DB_USER=your_db_user,DB_PASSWORD=your_db_password,CLOUD_SQL_CONNECTION_NAME=[PROJECT_ID]:[REGION]:[INSTANCE_NAME] \
  --platform managed \
  --region [REGION] \
  --allow-unauthenticated
```

## 5. Update Your PHP Config
- Use the environment variables for DB connection in your PHP code
- Host should be `127.0.0.1` for Cloud SQL Proxy

## 6. Grant Cloud Run Service Account Access to Cloud SQL
- Go to IAM & Admin > Service Accounts
- Find the Cloud Run service account
- Grant `Cloud SQL Client` role

## 7. Test Your Deployment
- Visit the Cloud Run URL
- Check logs for errors

---
See Google docs for [Cloud Run + Cloud SQL](https://cloud.google.com/sql/docs/mysql/connect-run) for more details.
