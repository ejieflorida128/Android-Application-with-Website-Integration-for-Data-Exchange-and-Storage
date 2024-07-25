package com.prgguru.example;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.ConnectException;
import java.net.UnknownHostException;
import java.util.ArrayList;
import java.util.HashMap;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class MainActivity extends Activity {
    private DBController controller;
    private ProgressDialog progressDialog;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Initialize DBController
        controller = new DBController(this);

        // Initialize ProgressDialog
        progressDialog = new ProgressDialog(MainActivity.this);
        progressDialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);
        progressDialog.setCancelable(false);

        // Retrieve event data from SQLite database
        ArrayList<HashMap<String, String>> eventList = controller.getAllEvents();

        // If event data exists, display it in ListView
        if (eventList.size() != 0) {
            ListAdapter adapter = new SimpleAdapter(MainActivity.this, eventList,
                    R.layout.view_event_entry, new String[]{"team_one", "team_one_score", "team_two", "team_two_score", "date", "time", "location"},
                    new int[]{R.id.teamOne, R.id.teamOneScore, R.id.teamTwo, R.id.teamTwoScore, R.id.date, R.id.time, R.id.location});
            ListView myList = findViewById(R.id.list);
            myList.setAdapter(adapter);

            // Show synchronization status
            Toast.makeText(getApplicationContext(), controller.getSyncStatus(),
                    Toast.LENGTH_LONG).show();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();
        if (id == R.id.refresh) {
            syncSQLiteMySQLDB();
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    // Method to handle refresh button click
    public void refreshData(View view) {
        syncSQLiteMySQLDB();
    }

    // Method to handle synchronize SQLite data with MySQL database
    public void syncSQLiteMySQLDB() {
        OkHttpClient client = new OkHttpClient();
        MediaType JSON = MediaType.parse("application/json; charset=utf-8");
        ArrayList<HashMap<String, String>> eventList = controller.getAllEvents();

        if (eventList.size() != 0) {
            if (controller.dbSyncCount() != 0) {
                progressDialog.setMessage("Syncing SQLite Data with Remote MySQL DB. Please wait...");
                progressDialog.show();

                String json = controller.composeJSONfromSQLite();

                if (json != null) {
                    RequestBody body = RequestBody.create(JSON, json.getBytes());

                    Request request = new Request.Builder()
                            .url("http://10.0.25.169/xampp/project_x/insert_event.php")
                            .post(body)
                            .build();

                    client.newCall(request).enqueue(new Callback() {
                        @Override
                        public void onFailure(Call call, IOException e) {
                            progressDialog.dismiss();
                            e.printStackTrace();
                            runOnUiThread(() -> {
                                if (e instanceof ConnectException || e instanceof UnknownHostException) {
                                    Toast.makeText(MainActivity.this, "Failed to connect to server. Please check your internet connection.", Toast.LENGTH_SHORT).show();
                                } else {
                                    Toast.makeText(MainActivity.this, "Request failed. Please try again later.", Toast.LENGTH_SHORT).show();
                                }
                            });
                        }

                        @Override
                        public void onResponse(Call call, Response response) throws IOException {
                            progressDialog.dismiss();
                            if (!response.isSuccessful()) {
                                throw new IOException("Unexpected code " + response);
                            }
                            String responseData = response.body().string();
                            try {
                                JSONArray arr = new JSONArray(responseData);
                                for (int i = 0; i < arr.length(); i++) {
                                    JSONObject obj = arr.getJSONObject(i);
                                    controller.updateSyncStatus(obj.getString("eventId"), obj.getString("status"));
                                }
                                runOnUiThread(() -> Toast.makeText(MainActivity.this, "DB Sync completed!", Toast.LENGTH_SHORT).show());
                            } catch (JSONException e) {
                                e.printStackTrace();
                                runOnUiThread(() -> Toast.makeText(MainActivity.this, "DB Sync failed!", Toast.LENGTH_SHORT).show());
                            }
                        }
                    });
                } else {
                    progressDialog.dismiss();
                    Toast.makeText(MainActivity.this, "Failed to compose JSON data.", Toast.LENGTH_SHORT).show();
                }
            } else {
                Toast.makeText(getApplicationContext(), "SQLite and Remote MySQL DBs are in Sync!", Toast.LENGTH_LONG).show();
            }
        } else {
            Toast.makeText(getApplicationContext(), "No data in SQLite DB to sync.", Toast.LENGTH_LONG).show();
        }
    }

    // Method to handle synchronize data from web and delete all data from SQLite
    public void SyncDataFromWebBased(View view) {
        controller.deleteAllData();
        fetchFromPHPMyAdminAndInsertToSQLite();
    }

    // Method to fetch data from PHPMyAdmin and insert into SQLite
    private void fetchFromPHPMyAdminAndInsertToSQLite() {
        OkHttpClient client = new OkHttpClient();
        Request request = new Request.Builder()
                .url("http://10.0.25.169/xampp/project_x/get_events.php") // Replace with your PHP script to fetch events from PHPMyAdmin
                .build();

        client.newCall(request).enqueue(new Callback() {
            @Override
            public void onFailure(Call call, IOException e) {
                e.printStackTrace();
                runOnUiThread(() -> Toast.makeText(MainActivity.this, "Failed to fetch data from PHPMyAdmin!", Toast.LENGTH_SHORT).show());
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException {
                if (!response.isSuccessful()) {
                    throw new IOException("Unexpected code " + response);
                }
                String responseData = response.body().string();
                try {
                    JSONArray jsonArray = new JSONArray(responseData);
                    for (int i = 0; i < jsonArray.length(); i++) {
                        JSONObject jsonObject = jsonArray.getJSONObject(i);
                        String teamOne = jsonObject.getString("team_one");
                        String teamOneScore = jsonObject.getString("team_one_score");
                        String teamTwo = jsonObject.getString("team_two");
                        String teamTwoScore = jsonObject.getString("team_two_score");
                        String date = jsonObject.getString("date");
                        String time = jsonObject.getString("time");
                        String location = jsonObject.getString("location");
                        // Insert fetched data into SQLite database
                        HashMap<String, String> eventData = new HashMap<>();
                        eventData.put("team_one", teamOne);
                        eventData.put("team_one_score", teamOneScore);
                        eventData.put("team_two", teamTwo);
                        eventData.put("team_two_score", teamTwoScore);
                        eventData.put("date", date);
                        eventData.put("time", time);
                        eventData.put("location", location);
                        controller.insertEvent(eventData);
                    }
                    runOnUiThread(() -> {
                        Toast.makeText(MainActivity.this, "Data synchronized from PHPMyAdmin to SQLite!", Toast.LENGTH_SHORT).show();
                        // Refresh ListView after syncing
                        refreshListView();
                    });
                } catch (JSONException e) {
                    e.printStackTrace();
                    runOnUiThread(() -> Toast.makeText(MainActivity.this, "Failed to parse data from PHPMyAdmin!", Toast.LENGTH_SHORT).show());
                }
            }

        });
    }

    // Method to refresh ListView after syncing
    private void refreshListView() {
        ArrayList<HashMap<String, String>> eventList = controller.getAllEvents();
        if (eventList.size() != 0) {
            ListAdapter adapter = new SimpleAdapter(MainActivity.this, eventList,
                    R.layout.view_event_entry, new String[]{"team_one", "team_one_score", "team_two", "team_two_score", "date", "time", "location"},
                    new int[]{R.id.teamOne, R.id.teamOneScore, R.id.teamTwo, R.id.teamTwoScore, R.id.date, R.id.time, R.id.location});
            ListView myList = findViewById(R.id.list);
            myList.setAdapter(adapter);

            // Show synchronization status
            Toast.makeText(getApplicationContext(), "Sync Status: " + controller.getSyncStatus(),
                    Toast.LENGTH_LONG).show();
        }
    }

    // Method to handle add event button click
    public void addEvent(View view) {
        Intent objIntent = new Intent(getApplicationContext(), NewEvent.class);
        startActivity(objIntent);
    }
}
