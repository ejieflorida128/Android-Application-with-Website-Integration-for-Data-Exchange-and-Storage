package com.prgguru.example;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.util.Log;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class DBController extends SQLiteOpenHelper {

    private Context mContext;

    private static final String DATABASE_NAME = "EventDb.db";
    private static final int DATABASE_VERSION = 1;

    public DBController(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
        mContext = context;
    }

    @Override
    public void onCreate(SQLiteDatabase database) {
        String query = "CREATE TABLE events ("
                + "eventId INTEGER PRIMARY KEY AUTOINCREMENT, "
                + "team_one TEXT, "
                + "team_one_score INTEGER, "
                + "team_two TEXT, "
                + "team_two_score INTEGER, "
                + "date TEXT, "
                + "time TEXT, "
                + "location TEXT, "
                + "updateStatus TEXT)";
        database.execSQL(query);
    }

    @Override
    public void onUpgrade(SQLiteDatabase database, int oldVersion, int newVersion) {
        String query = "DROP TABLE IF EXISTS events";
        database.execSQL(query);
        onCreate(database);
    }

    public void insertEvent(HashMap<String, String> eventData) {
        SQLiteDatabase database = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put("team_one", eventData.get("team_one"));
        values.put("team_one_score", eventData.get("team_one_score"));
        values.put("team_two", eventData.get("team_two"));
        values.put("team_two_score", eventData.get("team_two_score"));
        values.put("date", eventData.get("date"));
        values.put("time", eventData.get("time"));
        values.put("location", eventData.get("location"));
        values.put("updateStatus", "no");
        long newRowId = database.insert("events", null, values);
        database.close();

        // Check if insertion was successful and update status
        if (newRowId != -1) {
            // Data inserted successfully, update status to indicate it needs synchronization
            String eventId = String.valueOf(newRowId);
            updateSyncStatus(eventId, "no");
        }
    }

    public ArrayList<HashMap<String, String>> getAllEvents() {
        ArrayList<HashMap<String, String>> eventList = new ArrayList<>();
        String selectQuery = "SELECT * FROM events";
        SQLiteDatabase database = this.getWritableDatabase();
        Cursor cursor = database.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                HashMap<String, String> event = new HashMap<>();
                event.put("eventId", cursor.getString(0));
                event.put("team_one", cursor.getString(1));
                event.put("team_one_score", cursor.getString(2));
                event.put("team_two", cursor.getString(3));
                event.put("team_two_score", cursor.getString(4));
                event.put("date", cursor.getString(5));
                event.put("time", cursor.getString(6));
                event.put("location", cursor.getString(7));
                eventList.add(event);
            } while (cursor.moveToNext());
        }
        cursor.close();
        database.close();
        return eventList;
    }

    public String composeJSONfromSQLite() {
        ArrayList<HashMap<String, String>> eventList = new ArrayList<>();
        String selectQuery = "SELECT * FROM events WHERE updateStatus = 'no'";
        SQLiteDatabase database = this.getWritableDatabase();
        Cursor cursor = database.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                HashMap<String, String> event = new HashMap<>();
                event.put("eventId", cursor.getString(0));
                event.put("team_one", cursor.getString(1));
                event.put("team_one_score", cursor.getString(2));
                event.put("team_two", cursor.getString(3));
                event.put("team_two_score", cursor.getString(4));
                event.put("date", cursor.getString(5));
                event.put("time", cursor.getString(6));
                event.put("location", cursor.getString(7));
                eventList.add(event);
            } while (cursor.moveToNext());
        }
        cursor.close();
        database.close();
        Gson gson = new GsonBuilder().create();
        return gson.toJson(eventList);
    }

    public void syncDataWithServer() {
        String url = "http://10.0.25.169/xampp/project_x/database_sync.php";
        String dataToSync = composeJSONfromSQLite();

        OkHttpClient client = new OkHttpClient();
        RequestBody body = RequestBody.create(MediaType.parse("application/json"), dataToSync);
        Request request = new Request.Builder()
                .url(url)
                .post(body)
                .build();

        client.newCall(request).enqueue(new Callback() {
            @Override
            public void onFailure(Call call, IOException e) {
                e.printStackTrace();
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException {
                if (response.isSuccessful()) {
                    String responseData = response.body().string();
                    Log.d("Sync Response", responseData);
                } else {
                    Log.e("Sync Error", "Unsuccessful response: " + response.message());
                }
            }
        });
    }

    public boolean isConnectedToInternet() {
        ConnectivityManager connectivityManager = (ConnectivityManager) mContext.getSystemService(Context.CONNECTIVITY_SERVICE);
        if (connectivityManager != null) {
            NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
            return activeNetworkInfo != null && activeNetworkInfo.isConnected();
        }
        return false;
    }

    public String getSyncStatus() {
        int syncCount = dbSyncCount();
        if (syncCount == 0) {
            return "SQLite and Remote MySQL DBs are in Sync!";
        } else {
            return "DB Sync needed";
        }
    }

    public int dbSyncCount() {
        int count = 0;
        String selectQuery = "SELECT * FROM events WHERE updateStatus = 'no'";
        SQLiteDatabase database = this.getWritableDatabase();
        Cursor cursor = database.rawQuery(selectQuery, null);
        count = cursor.getCount();
        cursor.close();
        database.close();
        return count;
    }

    public void updateSyncStatus(String id, String status) {
        SQLiteDatabase database = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put("updateStatus", status);
        database.update("events", values, "eventId = ?", new String[]{id});
        database.close();
    }

    public void deleteAllData() {
        SQLiteDatabase database = this.getWritableDatabase();
        database.delete("events", null, null);
        database.close();
    }
}
