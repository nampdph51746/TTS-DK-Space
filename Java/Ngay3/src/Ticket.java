import java.time.LocalDateTime;

public class Ticket {
    private String userEmail;
    private String eventId;
    private int seatNumber;
    private LocalDateTime bookingTime;

    public Ticket(String userEmail, String eventId, int seatNumber) {
        this.userEmail = userEmail;
        this.eventId = eventId;
        this.seatNumber = seatNumber;
        this.bookingTime = LocalDateTime.now();
    }

    public String getUserEmail() {
        return userEmail;
    }

    public String getEventId() {
        return eventId;
    }

    public int getSeatNumber() {
        return seatNumber;
    }

    public LocalDateTime getBookingTime() {
        return bookingTime;
    }

    @Override
    public String toString() {
        return "Ticket: Email: " + userEmail + ", Event ID: " + eventId +
               ", Seat: " + seatNumber + ", Booked at: " + bookingTime;
    }
}