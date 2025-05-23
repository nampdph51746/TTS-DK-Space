import java.time.LocalDate;
import java.util.*;

public class EventBookingSystem {
    private ArrayList<Event> events = new ArrayList<>();
    private LinkedList<Ticket> tickets = new LinkedList<>();
    private Scanner scanner = new Scanner(System.in);

    // Thêm sự kiện mới
    public void addEvent() {
        System.out.print("Enter Event ID: ");
        String eventId = scanner.nextLine();
        System.out.print("Enter Event Name: ");
        String eventName = scanner.nextLine();
        System.out.print("Enter Location: ");
        String location = scanner.nextLine();
        System.out.print("Enter Date (YYYY-MM-DD): ");
        String dateInput = scanner.nextLine();
        LocalDate date;
        try {
            date = LocalDate.parse(dateInput); 
        } catch (Exception e) {
            System.out.println("Error: Invalid date format. Use YYYY-MM-DD.");
            return;
        }
        System.out.print("Enter Max Seats: ");
        String maxSeatsInput = scanner.nextLine();
        int maxSeats;
        try {
            maxSeats = Integer.parseInt(maxSeatsInput); 
            if (maxSeats <= 0) {
                System.out.println("Error: Max seats must be positive.");
                return;
            }
        } catch (NumberFormatException e) {
            System.out.println("Error: Invalid number format for max seats.");
            return;
        }

        Event event = new Event(eventId, eventName, location, date, maxSeats);
        events.add(event);
        System.out.println("Event added successfully: " + event);
    }

    // Đặt vé
    public void bookTicket() {
        System.out.print("Enter Event ID: ");
        String eventId = scanner.nextLine();
        Event event = findEventById(eventId);
        if (event == null) {
            System.out.println("Error: Event not found.");
            return;
        }

        System.out.print("Enter User Email: ");
        String userEmail = scanner.nextLine();
        System.out.print("Enter Seat Number (1-" + event.getMaxSeats() + "): ");
        String seatInput = scanner.nextLine();
        int seatNumber;
        try {
            seatNumber = Integer.parseInt(seatInput); 
            if (seatNumber < 1 || seatNumber > event.getMaxSeats()) {
                System.out.println("Error: Invalid seat number.");
                return;
            }
        } catch (NumberFormatException e) {
            System.out.println("Error: Invalid number format for seat.");
            return;
        }

        // Kiểm tra trùng ghế với HashSet
        HashSet<Integer> bookedSeats = getBookedSeatsForEvent(eventId);
        if (bookedSeats.contains(seatNumber)) {
            System.out.println("Error: Seat " + seatNumber + " is already booked.");
            return;
        }

        Ticket ticket = new Ticket(userEmail, eventId, seatNumber);
        tickets.add(ticket);
        System.out.println("Ticket booked successfully: " + ticket);
    }

    // Tìm sự kiện theo ID
    private Event findEventById(String eventId) {
        for (Event event : events) {
            if (event.getEventId().equals(eventId)) {
                return event;
            }
        }
        return null;
    }

    // Lấy danh sách ghế đã đặt cho một sự kiện
    private HashSet<Integer> getBookedSeatsForEvent(String eventId) {
        HashSet<Integer> bookedSeats = new HashSet<>();
        for (Ticket ticket : tickets) {
            if (ticket.getEventId().equals(eventId)) {
                bookedSeats.add(ticket.getSeatNumber());
            }
        }
        return bookedSeats;
    }

    // Tìm kiếm sự kiện theo tên
    public void searchEventByName() {
        System.out.print("Enter keyword to search event name: ");
        String keyword = scanner.nextLine();
        boolean found = false;
        for (Event event : events) {
            if (event.getEventName().toLowerCase().contains(keyword.toLowerCase())) {
                System.out.println(event);
                found = true;
            }
        }
        if (!found) {
            System.out.println("No events found with name containing: " + keyword);
        }
    }

    // Sắp xếp sự kiện theo tên
    public void sortEventsByName() {
        events.sort((e1, e2) -> e1.getEventName().compareToIgnoreCase(e2.getEventName()));
        System.out.println("Events sorted by name:");
        displayEvents();
    }

    // Hiển thị danh sách sự kiện
    public void displayEvents() {
        if (events.isEmpty()) {
            System.out.println("No events available.");
            return;
        }
        System.out.println("List of Events:");
        for (Event event : events) {
            System.out.println(event);
        }
    }

    // Duyệt danh sách vé theo thứ tự đặt 
    public void displayTickets() {
        if (tickets.isEmpty()) {
            System.out.println("No tickets booked.");
            return;
        }
        System.out.println("List of Tickets (in order of booking):");
        Iterator<Ticket> iterator = tickets.iterator();
        while (iterator.hasNext()) {
            System.out.println(iterator.next());
        }
    }

    // Thống kê lượt đặt vé theo sự kiện
    public void displayBookingStats() {
        HashMap<String, Integer> bookingStats = new HashMap<>();
        for (Ticket ticket : tickets) {
            String eventId = ticket.getEventId();
            bookingStats.put(eventId, bookingStats.getOrDefault(eventId, 0) + 1);
        }

        if (bookingStats.isEmpty()) {
            System.out.println("No bookings yet.");
            return;
        }

        System.out.println("Booking Statistics by Event:");
        for (Map.Entry<String, Integer> entry : bookingStats.entrySet()) {
            Event event = findEventById(entry.getKey());
            String eventName = (event != null) ? event.getEventName() : "Unknown Event";
            System.out.println("Event ID: " + entry.getKey() + ", Name: " + eventName +
                               ", Tickets Booked: " + entry.getValue());
        }
    }

    // Menu chính
    public void showMenu() {
        while (true) {
            System.out.println("\n=== Event Booking System ===");
            System.out.println("1. Add new event");
            System.out.println("2. Book a ticket");
            System.out.println("3. Search events by name");
            System.out.println("4. Sort events by name");
            System.out.println("5. Display all events");
            System.out.println("6. Display all tickets");
            System.out.println("7. Display booking statistics");
            System.out.println("8. Exit");
            System.out.print("Enter your choice: ");

            String choiceInput = scanner.nextLine();
            int choice;
            try {
                choice = Integer.parseInt(choiceInput);
            } catch (NumberFormatException e) {
                System.out.println("Error: Please enter a valid number.");
                continue;
            }

            switch (choice) {
                case 1:
                    addEvent();
                    break;
                case 2:
                    bookTicket();
                    break;
                case 3:
                    searchEventByName();
                    break;
                case 4:
                    sortEventsByName();
                    break;
                case 5:
                    displayEvents();
                    break;
                case 6:
                    displayTickets();
                    break;
                case 7:
                    displayBookingStats();
                    break;
                case 8:
                    System.out.println("Goodbye!");
                    scanner.close();
                    return;
                default:
                    System.out.println("Invalid choice. Please try again.");
            }
        }
    }

    public static void main(String[] args) {
        EventBookingSystem system = new EventBookingSystem();
        system.showMenu();
    }
}