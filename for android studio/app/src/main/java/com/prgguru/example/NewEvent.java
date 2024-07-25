package com.prgguru.example;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;
import java.util.HashMap;

public class NewEvent extends Activity {
    EditText teamOne, teamOneScore, teamTwo, teamTwoScore, date, time, location;
    DBController controller = new DBController(this);

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.add_new_user);

        teamOne = findViewById(R.id.TEAMONE);
        teamOneScore = findViewById(R.id.TeamOneScore);
        teamTwo = findViewById(R.id.TEAMTWO);
        teamTwoScore = findViewById(R.id.TeamTwoScore);
        date = findViewById(R.id.DATE);
        time = findViewById(R.id.TIME);
        location = findViewById(R.id.LOCATION);
    }

    /**
     * Called when Save button is clicked
     * @param view
     */
    public void AddNewEvent(View view) {
        HashMap<String, String> queryValues = new HashMap<>();
        queryValues.put("team_one", teamOne.getText().toString());
        queryValues.put("team_one_score", teamOneScore.getText().toString());
        queryValues.put("team_two", teamTwo.getText().toString());
        queryValues.put("team_two_score", teamTwoScore.getText().toString());
        queryValues.put("date", date.getText().toString());
        queryValues.put("time", time.getText().toString());
        queryValues.put("location", location.getText().toString());

        if (teamOne.getText().toString().trim().length() != 0 &&
                teamTwo.getText().toString().trim().length() != 0 &&
                date.getText().toString().trim().length() != 0 &&
                time.getText().toString().trim().length() != 0 &&
                location.getText().toString().trim().length() != 0) {
            controller.insertEvent(queryValues);
            this.callHomeActivity(view);
        } else {
            Toast.makeText(getApplicationContext(), "Please fill in all fields",
                    Toast.LENGTH_LONG).show();
        }
    }

    /**
     * Navigate to Home Screen
     * @param view
     */
    public void callHomeActivity(View view) {
        Intent objIntent = new Intent(getApplicationContext(), MainActivity.class);
        startActivity(objIntent);
    }

    /**
     * Called when Cancel button is clicked
     * @param view
     */
    public void cancelAddEvent(View view) {
        this.callHomeActivity(view);
    }
}
