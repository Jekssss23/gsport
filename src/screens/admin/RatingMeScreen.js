import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    FlatList,
    ActivityIndicator,
    TouchableOpacity
} from 'react-native';
import { collection, query, where, getDocs, orderBy } from 'firebase/firestore';
import { db, auth } from '../../config/firebase';
import { theme } from '../../styles/theme';
import { Ionicons } from '@expo/vector-icons';
import { BookingService } from '../../services/BookingService';

export default function RatingMeScreen({ navigation }) {
    const [ratings, setRatings] = useState([]);
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState({ average: 0, total: 0 });

    useEffect(() => {
        fetchRatings();
    }, []);

    const fetchRatings = async () => {
        try {
            setLoading(true);
            // Fetch employee ID from 'employees' collection using email or authUid
            const employeesRef = collection(db, 'employees');
            const qEmp = query(employeesRef, where('email', '==', auth.currentUser.email));
            const empSnapshot = await getDocs(qEmp);

            if (empSnapshot.empty) {
                setLoading(false);
                return;
            }

            const employeeId = empSnapshot.docs[0].id;
            const data = await BookingService.getEmployeeRatings(employeeId);

            setRatings(data);

            // Calculate stats
            if (data.length > 0) {
                const sum = data.reduce((acc, curr) => acc + (curr.staffRating || 0), 0);
                setStats({
                    average: (sum / data.length).toFixed(1),
                    total: data.length
                });
            }
        } catch (error) {
            console.error('Error fetching ratings:', error);
        } finally {
            setLoading(false);
        }
    };

    const renderRatingItem = ({ item }) => (
        <View style={styles.ratingCard}>
            <View style={styles.cardHeader}>
                <View style={styles.userInfo}>
                    <Text style={styles.userName}>{item.userName || 'Anonymous User'}</Text>
                    <Text style={styles.dateText}>
                        {item.createdAt?.toDate ? item.createdAt.toDate().toLocaleDateString() : 'Recent'}
                    </Text>
                </View>
                <View style={styles.starRow}>
                    {[1, 2, 3, 4, 5].map((star) => (
                        <Ionicons
                            key={star}
                            name={star <= item.staffRating ? "star" : "star-outline"}
                            size={16}
                            color={star <= item.staffRating ? "#FFD700" : "#555"}
                        />
                    ))}
                </View>
            </View>
            <Text style={styles.feedbackText}>
                {item.review || 'No written feedback provided.'}
            </Text>
            <View style={styles.facilityInfo}>
                <Ionicons name="location-outline" size={14} color={theme.colors.textSecondary} />
                <Text style={styles.facilityText}>{item.facilityName}</Text>
            </View>
        </View>
    );

    if (loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color={theme.colors.primary} />
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <View style={styles.statsHeader}>
                <View style={styles.statsMain}>
                    <Text style={styles.averageText}>{stats.average}</Text>
                    <View style={styles.averageStars}>
                        {[1, 2, 3, 4, 5].map((star) => (
                            <Ionicons
                                key={star}
                                name={star <= Math.round(stats.average) ? "star" : "star-outline"}
                                size={20}
                                color={star <= Math.round(stats.average) ? "#FFD700" : "#555"}
                            />
                        ))}
                    </View>
                </View>
                <View style={styles.statsSub}>
                    <Text style={styles.totalText}>Berdasarkan {stats.total} Penilaian</Text>
                    <Text style={styles.subHint}>Rating dari User APK</Text>
                </View>
            </View>

            <FlatList
                data={ratings}
                keyExtractor={(item) => item.id}
                renderItem={renderRatingItem}
                contentContainerStyle={styles.listContent}
                ListEmptyComponent={
                    <View style={styles.emptyContainer}>
                        <Ionicons name="star-half-outline" size={60} color="#333" />
                        <Text style={styles.emptyText}>Belum ada rating untuk Anda.</Text>
                    </View>
                }
                onRefresh={fetchRatings}
                refreshing={loading}
            />
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: theme.colors.background,
    },
    loadingContainer: {
        flex: 1,
        backgroundColor: theme.colors.background,
        justifyContent: 'center',
        alignItems: 'center',
    },
    statsHeader: {
        padding: 30,
        backgroundColor: 'rgba(255,0,0,0.05)',
        borderBottomWidth: 1,
        borderBottomColor: 'rgba(255,255,255,0.05)',
        alignItems: 'center',
    },
    statsMain: {
        alignItems: 'center',
        marginBottom: 10,
    },
    averageText: {
        fontSize: 48,
        fontWeight: 'bold',
        color: 'white',
    },
    averageStars: {
        flexDirection: 'row',
        gap: 4,
    },
    statsSub: {
        alignItems: 'center',
    },
    totalText: {
        color: theme.colors.text,
        fontSize: 16,
        fontWeight: '600',
    },
    subHint: {
        color: theme.colors.textSecondary,
        fontSize: 12,
        marginTop: 4,
    },
    listContent: {
        padding: 20,
    },
    ratingCard: {
        backgroundColor: 'rgba(255,255,255,0.03)',
        borderRadius: 15,
        padding: 16,
        marginBottom: 16,
        borderWidth: 1,
        borderColor: 'rgba(255,255,255,0.05)',
    },
    cardHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
        marginBottom: 12,
    },
    userInfo: {
        flex: 1,
    },
    userName: {
        color: 'white',
        fontSize: 16,
        fontWeight: 'bold',
    },
    dateText: {
        color: theme.colors.textSecondary,
        fontSize: 12,
        marginTop: 2,
    },
    starRow: {
        flexDirection: 'row',
        gap: 2,
    },
    feedbackText: {
        color: theme.colors.text,
        fontSize: 14,
        lineHeight: 20,
        marginBottom: 12,
    },
    facilityInfo: {
        flexDirection: 'row',
        alignItems: 'center',
        gap: 6,
    },
    facilityText: {
        color: theme.colors.textSecondary,
        fontSize: 12,
    },
    emptyContainer: {
        alignItems: 'center',
        marginTop: 50,
    },
    emptyText: {
        color: '#666',
        fontSize: 16,
        marginTop: 15,
    },
});
