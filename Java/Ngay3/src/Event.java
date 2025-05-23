import java.time.LocalDate;

public class Event {
    private String eventId;
    private String eventName;
    private String location;
    private LocalDate date;
    private int maxSeats;

    public Event(String eventId, String eventName, String location, LocalDate date, int maxSeats) {
        this.eventId = eventId;
        this.eventName = eventName;
        this.location = location;
        this.date = date;
        this.maxSeats = maxSeats;
    }

    public String getEventId() {
        return eventId;
    }

    public String getEventName() {
        return eventName;
    }

    public void setEventName(String eventName) {
        this.eventName = eventName;
    }

    public String getLocation() {
        return location;
    }

    public void setLocation(String location) {
        this.location = location;
    }

    public LocalDate getDate() {
        return date;
    }

    public void setDate(LocalDate date) {
        this.date = date;
    }

    public int getMaxSeats() {
        return maxSeats;
    }

    public void setMaxSeats(int maxSeats) {
        this.maxSeats = maxSeats;
    }

    @Override
    public String toString() {
        return "Event ID: " + eventId + ", Name: " + eventName + ", Location: " + location +
               ", Date: " + date + ", Max Seats: " + maxSeats;
    }
}